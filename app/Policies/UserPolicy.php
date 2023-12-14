<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    use HandlesAuthorization;

    public function destroy(User $loggedInUser, User $targetUser) {
        return $loggedInUser->id === 11 && $loggedInUser->id !== $targetUser->id;
    }
}
