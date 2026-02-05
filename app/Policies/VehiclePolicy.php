<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\Response;

class VehiclePolicy
{
    public function viewAny(User $user)
    {
        return true; 
    }

    public function view(User $user, Vehicle $vehicle)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->can('can.create.vehicle');
    }

    public function update(User $user, Vehicle $vehicle)
    {
        return $user->can('can.edit.vehicle');
    }

    public function delete(User $user, Vehicle $vehicle)
    {
        return $user->can('can.delete.vehicle');
    }
}