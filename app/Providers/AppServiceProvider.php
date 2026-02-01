<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;
use App\Policies\PermissionPolicy;

use App\Models\Opportunity;
use App\Policies\OpportunityPolicy;
use App\Models\OpportunityFollowup;
use App\Policies\OpportunityFollowupPolicy;

use App\Services\SidebarData;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policies
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Opportunity::class, OpportunityPolicy::class);
        Gate::policy(OpportunityFollowup::class, OpportunityFollowupPolicy::class);

        // Sidebar composer
        View::composer('components.sidebar', function ($view) {
            $data = app(SidebarData::class)->forUser(auth()->user());
            $view->with($data);
        });
        
        /*// View Composer: sidebar
        View::composer('components.sidebar', function ($view) {

            // Invitados: no sidebar con datos
            if (!auth()->check()) {
                $view->with('overdueCount', 0);
                return;
            }

            $user = auth()->user();

            // Si no puede ver agenda, no hace falta calcular (ahorra query)
            if (!$user->can('agenda.view')) {
                $view->with('overdueCount', 0);
                return;
            }

            $cacheKey = "sidebar_overdue_count_user_{$user->id}";
            $today = Carbon::today()->toDateString();

            $overdueCount = Cache::remember($cacheKey, 60, function () use ($user, $today) {

                // Subquery: último followup por oportunidad
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

                // Conteo: oportunidades del usuario cuyo next_contact_date < hoy
                return DB::table('opportunities as o')
                    ->leftJoinSub($lastFollowupData, 'lfd', function ($join) {
                        $join->on('lfd.opportunity_id', '=', 'o.id');
                    })
                    ->where('o.assigned_user_id', $user->id)
                    ->whereNotNull('lfd.next_contact_date')
                    ->whereDate('lfd.next_contact_date', '<', $today)
                    ->count();
            });

            $view->with('overdueCount', (int)$overdueCount);
        });*/
    }
}
