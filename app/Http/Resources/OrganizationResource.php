<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "organization",
            "id"   => $this->key,

            "attributes" => [
                "name"          => $this->name,
                "description"   => $this->description,
                "address"       => $this->address,
            ],

            "relationships" => [
                "users" => UserResource::collection($this->whenLoaded("users")),
                "tags"  => TagResource::collection($this->whenLoaded("tags")),
            ],

            "links" => [
                "parent" => route("organization.index"),
                "this"   => route("organization.show", $this->key)
            ]
        ];
    }
}
