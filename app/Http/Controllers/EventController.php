<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends Controller
{
    public function index() {
        $events = QueryBuilder::for(Event::class)
            ->allowedFilters(["name",
                AllowedFilter::scope("starts_before"),
                AllowedFilter::scope("location")
            ])
            ->allowedIncludes(["organizations"])
            ->get();

        return EventResource::collection($events);
    }

    public function show(Event $event) {
        return new EventResource($event->load(["organizations"]));
    }

    private function grantPermission($user, $organizations) {

        $granted = false;
        $organizationsArr = [];
        collect($organizations)->each(function ($organization) use ($user, &$granted, &$organizationsArr) {
            $currentOrganization = Organization::where("key", $organization)->first();
            $organizationsArr[] = $currentOrganization;
            $granted = Gate::forUser($user)->allows("modifyOrganization", $currentOrganization);
        });

        return ["granted" => $granted, "organizations" => $organizationsArr];
    }

    public function store(EventStoreRequest $request) {
        $collectedData = $this->grantPermission(Auth::guard("sanctum")->user(), $request->organizations);
        if(!$collectedData["granted"]) abort(403);

        $event = Event::create(Arr::except($request->validated(), "organizations"));
        $event->organizations()->sync($collectedData["organizations"]);

        return new EventResource($event->load(["organizations"]));
    }

    public function update(EventUpdateRequest  $request, Event $event) {
        $collectedData = $this->grantPermission(Auth::guard("sanctum")->user(),
            $request->organizations != null ? $request->organizations : $event->organizations->pluck("key")->toArray());

        if(!$collectedData["granted"]) abort(403);

        $event->update(Arr::except($request->validated(), "organizations"));
        if($request->has("organizations"))
            $event->organizations()->sync($collectedData["organizations"]);

        $event->save();
        return new EventResource($event->load(["organizations"]));
    }

    public function delete(Event $event) {
        if (Gate::forUser(Auth::guard("sanctum")->user())->denies("modifyEvent", $event)) abort(403);
        $event->delete();
        return response()->json(null, 204);
    }
}
