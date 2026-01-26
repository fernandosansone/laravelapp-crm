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
        // Permiso mínimo para ver agenda: ver oportunidades o seguimientos
        if (!$request->user()->can('opportunities.view') && !$request->user()->can('followups.view')) {
            abort(403);
        }

        $today = Carbon::today();

        // Cargar oportunidades asignadas al usuario con su último followup
        $opps = Opportunity::query()
            ->where('assigned_user_id', $request->user()->id)
            ->with(['contact', 'followups' => function ($q) {
                $q->orderByDesc('contact_date')->limit(1);
            }])
            ->orderByDesc('id')
            ->get();

        // Armar filas de agenda (calculando atraso)
        $rows = $opps->map(function ($o) use ($today) {
            $last = $o->followups->first(); // por el limit(1)
            $next = $last?->next_contact_date ? Carbon::parse($last->next_contact_date) : null;

            $daysLate = null;
            if ($next) {
                $daysLate = $next->isPast() ? $next->startOfDay()->diffInDays($today, false) : 0;
                // diffInDays con false => negativos si next > today, pero acá lo dejamos simple:
                $daysLate = $today->diffInDays($next, false) * -1; // hoy - next (positivo si atrasado)
                if ($daysLate < 0) $daysLate = 0;
            }

            return [
                'opportunity' => $o,
                'last_followup' => $last,
                'next_contact_date' => $next?->toDateString(),
                'days_late' => $daysLate, // null si no hay próxima fecha
            ];
        });

        // Separar en 3 grupos: atrasados, hoy, sin fecha
        $overdue = $rows->filter(fn($r) => $r['next_contact_date'] && $r['days_late'] > 0)->values();
        $todayRows = $rows->filter(fn($r) => $r['next_contact_date'] && $r['days_late'] === 0 && $r['next_contact_date'] === $today->toDateString())->values();
        $noDate = $rows->filter(fn($r) => !$r['next_contact_date'])->values();

        return view('agenda.index', compact('overdue', 'todayRows', 'noDate'));
    }

    public function storeFollowup(Request $request)
    {
        Gate::authorize('create', [OpportunityFollowup::class, $opp]);

        $data = $request->validate([
            'opportunity_id' => ['required','integer','exists:opportunities,id'],
            'contact_date' => ['required','date'],
            'contact_method' => ['required','string','max:50'],
            'response' => ['nullable','string'],
            'next_contact_date' => ['nullable','date'],
        ]);

        $opp = Opportunity::findOrFail($data['opportunity_id']);

        OpportunityFollowup::create([
            'opportunity_id' => $opp->id,
            'contact_date' => $data['contact_date'],
            'contact_method' => $data['contact_method'],
            'response' => $data['response'] ?? null,
            'next_contact_date' => $data['next_contact_date'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('agenda.index')->with('success', 'Seguimiento registrado.');
    }
}
