<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today = Carbon::today()->toDateString();

        // KPIs básicos
        $contactsTotal = DB::table('contacts')->count();

        $myOppTotal = DB::table('opportunities')
            ->where('assigned_user_id', $userId)
            ->count();

        $myOppOpen = DB::table('opportunities')
            ->where('assigned_user_id', $userId)
            ->whereNotIn('status', ['ganada', 'perdida'])
            ->count();

        $myOppInQuote = DB::table('opportunities')
            ->where('assigned_user_id', $userId)
            ->where('status', 'cotizacion')
            ->count();

        // Subquery último followup por oportunidad
        $latestFollowup = DB::table('opportunity_followups as f')
            ->select('f.opportunity_id', DB::raw('MAX(f.contact_date) as last_contact_date'))
            ->groupBy('f.opportunity_id');

        // Next_contact_date del followup más reciente
        $lastFollowupData = DB::table('opportunity_followups as f')
            ->joinSub($latestFollowup, 'lf', function ($join) {
                $join->on('lf.opportunity_id', '=', 'f.opportunity_id')
                    ->on('lf.last_contact_date', '=', 'f.contact_date');
            })
            ->select('f.opportunity_id', 'f.next_contact_date', 'f.contact_method', 'f.response');

        // Atrasados (para el usuario)
        $overdue = DB::table('opportunities as o')
            ->joinSub($lastFollowupData, 'lfd', function ($join) {
                $join->on('lfd.opportunity_id', '=', 'o.id');
            })
            ->join('contacts as c', 'c.id', '=', 'o.contact_id')
            ->where('o.assigned_user_id', $userId)
            ->whereNotNull('lfd.next_contact_date')
            ->whereDate('lfd.next_contact_date', '<', $today)
            ->orderBy('lfd.next_contact_date')
            ->limit(8)
            ->get([
                'o.id as opportunity_id',
                'o.detail',
                'o.status',
                'o.amount',
                'c.first_name',
                'c.last_name',
                'c.company_name',
                'lfd.next_contact_date',
            ]);

        $overdueCount = DB::table('opportunities as o')
            ->joinSub($lastFollowupData, 'lfd', function ($join) {
                $join->on('lfd.opportunity_id', '=', 'o.id');
            })
            ->where('o.assigned_user_id', $userId)
            ->whereNotNull('lfd.next_contact_date')
            ->whereDate('lfd.next_contact_date', '<', $today)
            ->count();

        // Agenda de hoy (next_contact_date = hoy)
        $todayAgenda = DB::table('opportunities as o')
            ->joinSub($lastFollowupData, 'lfd', function ($join) {
                $join->on('lfd.opportunity_id', '=', 'o.id');
            })
            ->join('contacts as c', 'c.id', '=', 'o.contact_id')
            ->where('o.assigned_user_id', $userId)
            ->whereDate('lfd.next_contact_date', '=', $today)
            ->orderBy('o.id', 'desc')
            ->limit(8)
            ->get([
                'o.id as opportunity_id',
                'o.detail',
                'o.status',
                'o.amount',
                'c.first_name',
                'c.last_name',
                'c.company_name',
                'lfd.next_contact_date',
            ]);

        // Pipeline (conteo por estado para el usuario)
        $pipeline = DB::table('opportunities')
            ->select('status', DB::raw('COUNT(*) as qty'))
            ->where('assigned_user_id', $userId)
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->keyBy('status');

        return view('dashboard', compact(
            'contactsTotal',
            'myOppTotal',
            'myOppOpen',
            'myOppInQuote',
            'overdue',
            'overdueCount',
            'todayAgenda',
            'pipeline'
        ));
    }
}
