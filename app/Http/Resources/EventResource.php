<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "event",
            "id"   => $this->key,
            "attributes" => [
                "name"          => $this->name,
                "description"   => $this->description,
                "location"      => $this->location,
                "banner"        => $this->banner,
                "starting_at"   => $this->starting_at,
                "ending_at"     => $this->ending_at,
                "updated_at"    => $this->updated_at,
                "created_at"    => $this->created_at,
            ],

            "relationships" => [
                "organizations" => OrganizationResource::collection($this->whenLoaded("organizations")),
            ],

            "links" => [
                "parent" => route("event.index"),
                "this"   => route("event.show", $this->key)
            ]
        ];
    }
}
