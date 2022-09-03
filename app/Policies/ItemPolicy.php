<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
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

    public function update(User $user, Item $item)
    {
        return $user->hasAdminRights($item->club_id);
    }

    public function delete(User $user, Item $item)
    {
        return $this->update($user, $item);
    }

}
