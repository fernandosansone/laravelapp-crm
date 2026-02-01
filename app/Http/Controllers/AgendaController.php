<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\OpportunityFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('agenda.view');

        $today = Carbon::today();

        $opps = Opportunity::query()
            ->where('assigned_user_id', $request->user()->id)
            ->with(['contact', 'followups' => function ($q) {
                $q->latest('contact_date')->limit(1);
            }])
            ->get();

        $rows = $opps->map(function ($o) use ($today) {
            $last = $o->followups->first();
            $next = $last?->next_contact_date
                ? Carbon::parse($last->next_contact_date)
                : null;

            $daysLate = null;
            if ($next) {
                $daysLate = $next->isPast()
                    ? $next->diffInDays($today)
                    : 0;
            }

            return compact('o', 'last', 'next', 'daysLate');
        });

        $overdue   = $rows->filter(fn($r) => $r['daysLate'] > 0)->values();
        $todayRows = $rows->filter(fn($r) => $r['daysLate'] === 0 && $r['next']?->isToday())->values();
        $noDate    = $rows->filter(fn($r) => !$r['next'])->values();

        return view('agenda.index', compact('overdue', 'todayRows', 'noDate'));
    }

    public function storeFollowup(Request $request)
    {
        $this->authorize('agenda.followups.create');

        $data = $request->validate([
            'opportunity_id' => ['required','exists:opportunities,id'],
            'contact_date' => ['required','date'],
            'contact_method' => ['required','string','max:50'],
            'response' => ['nullable','string'],
            'next_contact_date' => ['nullable','date'],
        ]);

        $opp = Opportunity::findOrFail($data['opportunity_id']);

        $this->authorize('create', [OpportunityFollowup::class, $opp]);

        OpportunityFollowup::create([
            'opportunity_id' => $opp->id,
            'contact_date' => $data['contact_date'],
            'contact_method' => $data['contact_method'],
            'response' => $data['response'] ?? null,
            'next_contact_date' => $data['next_contact_date'] ?? null,
            'created_by' => $request->user()->id,
        ]);
        
        app(\App\Services\SidebarData::class)->forgetOverdueCacheFor($request->user());

        return back()->with('success', 'Seguimiento registrado.');
    }
}
