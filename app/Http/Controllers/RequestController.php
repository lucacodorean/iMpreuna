<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStoreRequest;
use App\Http\Requests\RequestUpdateRequest;
use App\Http\Resources\UserRequestResource;
use App\Models\Tag;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class RequestController extends Controller {

    public function index() {
        $requests = QueryBuilder::for(UserRequest::class)
            ->allowedFilters(["tags", "status"])
            ->allowedIncludes(["tags", "user", "volunteers"])
            ->defaultSorts("-created_at", "-status")
            ->allowedSorts('created_at', 'updated_at')
            ->get();

        return UserRequestResource::collection($requests);
    }

    public function show(UserRequest $userRequest) {
        if(!(
            Gate::forUser(Auth::guard("sanctum")->user())->denies("viewRequest", $userRequest) ||
            Gate::forUser(Auth::guard("sanctum")->user())->denies("volunteerRequest", $userRequest)
        )) abort(403);
        return new UserRequestResource($userRequest->load(["tags", "user", "volunteers"] ));
    }

    public function store(RequestStoreRequest $request) {
        $userRequest = UserRequest::create(array_merge(
            Arr::except($request->validated(), ["tags"]), ["requester_id" => $request->user()->id ?? 1, "status" => false]
        ));

        if($request->has("tags"))
            $userRequest->tags()->attach(collect($request->tags)->map(fn($tag) => Tag::where("key", $tag)->first()->id)->toArray());

        return new UserRequestResource($userRequest->load(["tags", "user", "volunteers"]));
    }

    public function update(UserRequest $userRequest, RequestUpdateRequest $httpRequest) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->allows("updateRequest", $userRequest)) {
            $userRequest->update(Arr::except($httpRequest->validated(), ['tags']));
            if($httpRequest->has("tags")) {
                $userRequest->tags()->sync(collect($httpRequest->tags)->map(fn($tag) => Tag::where("key", $tag)->first()->id)->toArray());
            }
        } else if(Gate::forUser(Auth::guard("sanctum")->user())->allows("volunteerRequest", $userRequest) && $httpRequest->has("status")) {
            $userRequest->status = $httpRequest->status;
            $userRequest->saveQuietly();
        } else abort(403, "Unable to update this request.");

        return new UserRequestResource($userRequest->load(["tags", "user", "volunteers"]));
    }

    public function join(UserRequest $userRequest) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->allows("joinRequest", $userRequest))
            $userRequest->volunteers()->attach(Auth::guard("sanctum")->user());
        else abort(403, "Unable to join this request.");
        return new UserRequestResource($userRequest->load(["tags", "user", "volunteers"]));
    }

    public function delete(UserRequest $userRequest) {
        if(Gate::forUser(Auth::guard("sanctum")->user())->denies("updateRequest", $userRequest)) abort(403);
        $userRequest->delete();
        return response(null, 204);
    }
}
