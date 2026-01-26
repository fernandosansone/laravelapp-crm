<?php

namespace App\Http\Controllers;

use App\Enums\OpportunityStatus;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function commercial(Request $request)
    {
        if (!$request->user()->can('reports.view')) {
            abort(403);
        }

        $today = Carbon::today();

        // Filtros
        $from = $request->query('from'); // YYYY-MM-DD
        $to   = $request->query('to');
        $status = $request->query('status', '');
        $sellerId = $request->query('seller_id', '');
        $q = trim((string)$request->query('q', ''));
        $onlyOverdue = (bool)$request->query('only_overdue', false);

        // Subquery: último followup por oportunidad (contact_date máx)
        $latestFollowup = DB::table('opportunity_followups as f')
            ->select('f.opportunity_id', DB::raw('MAX(f.contact_date) as last_contact_date'))
            ->groupBy('f.opportunity_id');

        // Subquery: traigo next_contact_date del followup más reciente
        $lastFollowupData = DB::table('opportunity_followups as f')
            ->joinSub($latestFollowup, 'lf', function ($join) {
                $join->on('lf.opportunity_id', '=', 'f.opportunity_id')
                     ->on('lf.last_contact_date', '=', 'f.contact_date');
            })
            ->select('f.opportunity_id', 'f.contact_date as last_contact_date', 'f.next_contact_date');

        $query = Opportunity::query()
            ->select([
                'opportunities.*',
                'c.first_name as c_first_name',
                'c.last_name as c_last_name',
                'c.company_name as c_company_name',
                'u.name as seller_name',
                DB::raw('lfd.last_contact_date as last_contact_date'),
                DB::raw('lfd.next_contact_date as next_contact_date'),
            ])
            ->join('contacts as c', 'c.id', '=', 'opportunities.contact_id')
            ->join('users as u', 'u.id', '=', 'opportunities.assigned_user_id')
            ->leftJoinSub($lastFollowupData, 'lfd', function ($join) {
                $join->on('lfd.opportunity_id', '=', 'opportunities.id');
            });

        // Filtros por fecha de apertura (opened_at)
        if ($from) $query->whereDate('opportunities.opened_at', '>=', $from);
        if ($to)   $query->whereDate('opportunities.opened_at', '<=', $to);

        if ($status !== '') $query->where('opportunities.status', $status);
        if ($sellerId !== '') $query->where('opportunities.assigned_user_id', $sellerId);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('opportunities.detail', 'like', "%{$q}%")
                    ->orWhere('c.first_name', 'like', "%{$q}%")
                    ->orWhere('c.last_name', 'like', "%{$q}%")
                    ->orWhere('c.company_name', 'like', "%{$q}%");
            });
        }

        if ($onlyOverdue) {
            // atrasado = next_contact_date < hoy
            $query->whereNotNull('lfd.next_contact_date')
                  ->whereDate('lfd.next_contact_date', '<', now()->toDateString());
        }

        $rows = $query
            ->orderByRaw("CASE WHEN lfd.next_contact_date IS NULL THEN 1 ELSE 0 END") // primero con fecha
            ->orderBy('lfd.next_contact_date')
            ->orderByDesc('opportunities.id')
            ->paginate(20)
            ->withQueryString();

        // KPIs (sobre el mismo filtro, sin paginado)
        $all = (clone $query)->get();

        $total = $all->count();
        $withNext = $all->whereNotNull('next_contact_date')->count();
        $overdue = $all->filter(function ($r) use ($today) {
            return $r->next_contact_date && Carbon::parse($r->next_contact_date)->lt($today);
        })->count();

        $byStatus = $all->groupBy('status')->map->count()->toArray();

        // KPIs por vendedor
        $bySeller = $all->groupBy('seller_name')->map(function ($group) use ($today) {
            $t = $group->count();
            $ov = $group->filter(fn($r) => $r->next_contact_date && Carbon::parse($r->next_contact_date)->lt($today))->count();
            $g = $group->where('status', 'ganada')->count();
            $p = $group->where('status', 'perdida')->count();
            return [
                'total' => $t,
                'overdue' => $ov,
                'ganadas' => $g,
                'perdidas' => $p,
            ];
        })->toArray();

        $statuses = OpportunityStatus::values();
        $sellers = User::orderBy('name')->get(['id','name']);

        return view('reports.commercial', compact(
            'rows','statuses','sellers',
            'from','to','status','sellerId','q','onlyOverdue',
            'total','withNext','overdue','byStatus','bySeller'
        ));
    }
}
