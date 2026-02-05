<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
   /**
     * ¿Quién puede crear un ticket?
     */
    public function create(User $user)
    {
        // Cualquiera con el permiso 'can.create.ticket' (Cliente o Admin)
        return $user->can('can.create.ticket');
    }

    /**
     * ¿Quién puede ver UN ticket concreto?
     */
    public function view(User $user, Ticket $ticket)
    {
        // 1. EL ADMIN (Jefe Supremo): Puede ver todo
        if ($user->can('can.view.all.tickets')) {
            return true;
        }

        // 2. EL DUEÑO (Cliente): Solo si tiene permiso Y es su ticket
        return $user->can('can.view.own.tickets') && $user->id === $ticket->user_id;
    }

    /**
     * ¿Quién puede responder/actualizar?
     * (Usaremos esta lógica si quisieras editar el ticket, o para validar mensajes)
     */
    public function update(User $user, Ticket $ticket)
    {
        if ($user->can('can.reply.any.ticket')) {
            return true;
        }

        return $user->can('can.reply.own.tickets') && $user->id === $ticket->user_id;
    }
}