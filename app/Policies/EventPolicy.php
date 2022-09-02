<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasAdminRights();
    }

    public function update(User $user, Event $event)
    {
        return $user->admin || ($event->club_id && $user->hasAdminRights($event->club_id));
    }

    public function delete(User $user, Event $event)
    {
        return $this->update($user, $event);
    }

}
