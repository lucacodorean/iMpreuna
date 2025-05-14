<?php

namespace App\Providers;

use App\Models\Organization;
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
            $orgTagIds = $user->organizations()
                ->with('tags:id')->get()
                ->pluck('tags')->flatten()
                ->pluck('id')->unique()
                ->toArray();

            $requestTagIds = $userRequest->tags->pluck('id')->toArray();
            return $userRequest->user() !== $user && count(array_intersect($orgTagIds, $requestTagIds)) > 0;
        });

        Gate::define("isAdmin", function (User $user) {
           return $user->is_admin;
        });

        Gate::define("modifyOrganization", function (User $user, Organization $organization) {
            return $user->is_admin || $user->getRoleInOrganization($organization->id)->name == "Administrator";
        });
    }
}

