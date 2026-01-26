<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Opportunity;
use App\Models\OpportunityFollowup;

class OpportunityFollowupPolicy
{
    /**
     * Ver seguimientos
     */
    public function viewAny(User $user): bool
    {
        return $user->can('followups.view');
    }

    /**
     * Crear seguimiento (regla general)
     */
    public function create(User $user, Opportunity $opportunity): bool
    {
        // Debe tener permiso
        if (!$user->can('followups.create')) {
            return false;
        }

        // Solo sobre oportunidades asignadas a él
        if ($opportunity->assigned_user_id !== $user->id) {
            return false;
        }

        // Si la oportunidad está finalizada, no permite seguimiento
        if ($opportunity->status && method_exists($opportunity->status, 'allowsFollowUp')) {
            return $opportunity->status->allowsFollowUp();
        }

        return true;
    }
}
