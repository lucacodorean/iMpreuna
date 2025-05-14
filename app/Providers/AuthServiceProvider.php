<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserRequest;
use App\Policies\UserRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        UserRequest::class => UserRequestPolicy::class,
    ];

    public function boot(): void {
        $this->registerPolicies();

        Gate::define('viewRequest', function (User $user, UserRequest $userRequest) {
            return $userRequest->user->id === $user->id || $userRequest->volunteers->contains($user);
        });


        Gate::define('updateRequest', function (User $user, UserRequest $userRequest) {
            return $userRequest->user->id === $user->id;
        });

        Gate::define('volunteerRequest', function (User $user, UserRequest $userRequest) {
            return $userRequest->volunteers->contains($user);
        });

        Gate::define("joinRequest", function (User $user, UserRequest $userRequest) {
            // TBE: When organization will be implemented, make sure that the user's organization
            // has at least one tag in common with the request.
            return $userRequest->user() !== $user;
        });
    }
}

