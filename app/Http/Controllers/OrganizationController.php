<?php

namespace App\Http\Controllers;

use App\Http\Requests\HireVolutneerRequest;
use App\Http\Requests\OrganizationStoreRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserRequestResource;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class OrganizationController extends Controller
{
    public function index() {
        $organizations = QueryBuilder::for(Organization::class)
            ->allowedIncludes(["tags", "users", "events"])
            ->allowedFilters(["tags", "users", "events"])
            ->allowedSorts("name")
            ->get();

        return OrganizationResource::collection($organizations);
    }

    public function show(Organization $organization) {
        return new UserRequestResource($organization->load(["tags", "users", "events"]));
    }

    public function store(OrganizationStoreRequest $request) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->denies("isAdmin")) abort(403);
        $organization = Organization::create(Arr::except($request->validated(), ["tags"]));

        if($request->has("tags"))
            $organization->tags()->attach(collect($request->tags)->map(fn($tag) => Tag::where("key", $tag)->first()->id)->toArray());

        return new OrganizationResource($organization->load(["tags"]));
    }

    public function update(OrganizationUpdateRequest $request, Organization $organization) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->denies("modifyOrganization", $organization)) abort(403);

        $organization->update(Arr::except($request->validated(), ["tags"]));
        if($request->has("tags"))
            $organization->tags()->sync(collect($request->tags)->map(fn($tag) => Tag::where("key", $tag)->first()->id)->toArray());
        $organization->save();

        return new OrganizationResource($organization->load(["tags"]));
    }

    public function delete(Organization $organization) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->denies("isAdmin")) abort(403);
        $organization->delete();
        return response(null, 204);
    }

    public function hire(Organization $organization, HireVolutneerRequest $request)
    {
        if (Gate::forUser(Auth::guard("sanctum")->user())->denies("modifyOrganization", $organization)) abort(403);

        $user = User::where("key", $request->user_id)->first();
        $role = Role::where("key", $request->role_id)->first();

        if($role != null)
            if ($user->organizations()->where('organization_id', $organization->id)->exists())
                $user->organizations()->updateExistingPivot($organization->id, ['role_id' => $role->id]);
            else
                $user->organizations()->attach($organization->id, ['role_id' => $role->id]);
        else $user->organizations()->detach($organization->id);

        return response([
            "message" => "Organization volunteers list updated."
        ], 200);
    }
}
