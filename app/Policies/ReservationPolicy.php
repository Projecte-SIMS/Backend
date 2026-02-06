<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation)
    {
        if ($user->can('can.view.all.reservations')) {
            return true;
        }
        return $user->id === $reservation->user_id;
    }

    public function create(User $user)
    {
        return $user->can('can.create.reservation');
    }


    public function cancel(User $user, Reservation $reservation)
    {
        if ($user->can('can.view.all.reservations')) { 
            return true;
        }

        return $user->can('can.cancel.reservation') && $user->id === $reservation->user_id;
    }

    public function activate(User $user, Reservation $reservation)
    {
        return $user->can('can.activate.reservation') && $user->id === $reservation->user_id;
    }


    public function finish(User $user, Reservation $reservation)
    {
        return $user->can('can.finish.reservation') && $user->id === $reservation->user_id;
    }

    public function forceFinish(User $user)
    {
        return $user->can('can.force.finish.reservation');
    }
}