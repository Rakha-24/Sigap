<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin'
            || $ticket->id_pelapor === $user->id
            || $ticket->assigned_agent_id === $user->id
            || ($user->role === 'agent' && $ticket->departemen_id === $user->departemen_id);
    }
}