<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
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

    public function update(User $user, Section $section)
    {
        return $user->admin || ($section->club_id && $user->hasAdminRights($section->club_id));
    }

    public function delete(User $user, Section $section)
    {
        return $this->update($user, $section);
    }

}
