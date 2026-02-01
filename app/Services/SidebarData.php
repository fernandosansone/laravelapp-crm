<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

class SidebarData
{
    /**
     * Devuelve data lista para la vista del sidebar:
     * - menu (items ya filtrados por permisos)
     * - overdueCount (badge atrasos agenda)
     * - userName / primaryRole
     */
    public function forUser(?User $user): array
    {
        if (!$user) {
            return [
                'menu' => [],
                'overdueCount' => 0,
                'userName' => null,
                'primaryRole' => null,
            ];
        }

        $overdueCount = $this->overdueCountFor($user);
        $primaryRole = $this->primaryRoleFor($user);

        return [
            'menu' => $this->menuFor($user, $overdueCount),
            'overdueCount' => (int) $overdueCount,
            'userName' => $user->name,
            'primaryRole' => $primaryRole,
        ];
    }

    /**
     * Construye items del menú, filtrados por permisos.
     * Cada item:
     *  - key: para active match
     *  - label
     *  - route: route name
     *  - icon: nombre para render en blade
     *  - badge: null|int|string
     *  - badgeVariant: "red"|"green"|"gray"
     */
    protected function menuFor(User $user, int $overdueCount): array
    {
        $items = [];

        // Dashboard
        if ($user->can('dashboard.view')) {
            $items[] = $this->item('dashboard', 'Dashboard', 'dashboard', 'dashboard');
        }

        // Agenda (con badge atrasos)
        if ($user->can('agenda.view')) {
            $items[] = $this->item(
                'agenda',
                'Agenda',
                'agenda.index',
                'calendar',
                $overdueCount,
                $overdueCount > 0 ? 'red' : 'green'
            );
        }

        // Contactos
        if ($user->can('contacts.view')) {
            $items[] = $this->item('contacts', 'Contactos', 'contacts.index', 'users');
        }

        // Oportunidades
        if ($user->can('opportunities.view')) {
            $items[] = $this->item('opportunities', 'Oportunidades', 'opportunities.index', 'doc');
        }

        // Reporte comercial
        if ($user->can('reports.view')) {
            $items[] = $this->item('reports', 'Reporte comercial', 'reports.commercial', 'chart', null, null, 'reports/commercial');
        }

        // Admin area (Users/Roles/Permisos)
        if ($user->can('users.view')) {
            $items[] = $this->item('users', 'Usuarios', 'users.index', 'user');
        }
        if ($user->can('roles.view')) {
            $items[] = $this->item('roles', 'Roles', 'roles.index', 'layers');
        }
        if ($user->can('permissions.view')) {
            $items[] = $this->item('permissions', 'Permisos', 'permissions.index', 'clock');
        }

        return $items;
    }

    protected function item(
        string $key,
        string $label,
        string $route,
        string $icon,
        int|string|null $badge = null,
        ?string $badgeVariant = null,
        ?string $activePathPrefix = null
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'route' => $route,
            'icon' => $icon,
            'badge' => $badge,
            'badgeVariant' => $badgeVariant,
            // para request()->is() (si no se pasa, usa $key)
            'activePathPrefix' => $activePathPrefix ?? $key,
        ];
    }

    /**
     * Badge atrasos: oportunidades del usuario cuyo followup más reciente tiene next_contact_date < hoy.
     * Cache por usuario (60s).
     */
    protected function overdueCountFor(User $user): int
    {
        // Si no puede ver agenda, no tiene sentido calcularlo
        if (!$user->can('agenda.view')) {
            return 0;
        }

        $cacheKey = "sidebar_overdue_count_user_{$user->id}";

        return (int) Cache::remember($cacheKey, 60, function () use ($user) {
            $today = Carbon::today()->toDateString();

            // Subquery: último followup por oportunidad (MAX(contact_date))
            $latestFollowup = DB::table('opportunity_followups as f')
                ->select('f.opportunity_id', DB::raw('MAX(f.contact_date) as last_contact_date'))
                ->groupBy('f.opportunity_id');

            // Subquery: next_contact_date del followup más reciente
            $lastFollowupData = DB::table('opportunity_followups as f')
                ->joinSub($latestFollowup, 'lf', function ($join) {
                    $join->on('lf.opportunity_id', '=', 'f.opportunity_id')
                        ->on('lf.last_contact_date', '=', 'f.contact_date');
                })
                ->select('f.opportunity_id', 'f.next_contact_date');

            return DB::table('opportunities as o')
                ->leftJoinSub($lastFollowupData, 'lfd', function ($join) {
                    $join->on('lfd.opportunity_id', '=', 'o.id');
                })
                ->where('o.assigned_user_id', $user->id)
                ->whereNotNull('lfd.next_contact_date')
                ->whereDate('lfd.next_contact_date', '<', $today)
                ->count();
        });
    }

    /**
     * Rol "principal" a mostrar: el primero alfabéticamente (estable),
     * o si tenés un método User::primaryRole() lo toma.
     */
    protected function primaryRoleFor(User $user): ?string
    {
        if (method_exists($user, 'primaryRole')) {
            try {
                return $user->primaryRole();
            } catch (\Throwable $e) {
                // fallback
            }
        }

        // Evita cargar relación si no está; si está cargada, la usa.
        if ($user->relationLoaded('roles')) {
            return $user->roles->pluck('name')->sort()->first();
        }

        // Spatie: roles() relationship
        try {
            return $user->roles()->pluck('name')->sort()->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Opcional: para invalidar cache cuando se registra un followup.
     */
    public function forgetOverdueCacheFor(User $user): void
    {
        Cache::forget("sidebar_overdue_count_user_{$user->id}");
    }
}
