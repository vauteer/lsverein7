<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isClubAdmin();
    }

    public function update(User $user, Subscription $subscription)
    {
        return $user->isClubAdmin() && $user->club_id === $subscription->club_id;
    }

    public function delete(User $user, Subscription $subscription)
    {
        return $user->isClubAdmin() && $user->club_id === $subscription->club_id;
    }

}
