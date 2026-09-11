<?php

namespace App\Policies;

use App\Models\SimulationScenario;
use App\Models\User;

class SimulationPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list active simulations
    }

    public function view(User $user, SimulationScenario $scenario): bool
    {
        return $scenario->is_active || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SimulationScenario $scenario): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SimulationScenario $scenario): bool
    {
        return $user->isAdmin() && $scenario->attempts()->count() === 0;
    }
}
