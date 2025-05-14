<?php

namespace App\Policies;

use App\Models\UserRequest;
use App\Models\User;

class UserRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user != null;
    }

    public function view(User $user, UserRequest $userRequest): bool
    {
        return $userRequest->requester == $user;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserRequest $userRequest): bool
    {
        return $userRequest->requester == $user;
    }

    public function delete(User $user, UserRequest $userRequest): bool
    {
        return $userRequest->requester == $user;
    }

    public function restore(User $user, UserRequest $userRequest): bool
    {
        return false;
    }

    public function forceDelete(User $user, UserRequest $userRequest): bool
    {
        return false;
    }
}
