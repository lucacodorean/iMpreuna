<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "type" => "request",
            "id"   => $this->key,
            "attributes" => [
                "description" => $this->description,
                "status"      => $this->status,
                "created_at"  => $this->created_at,
                "updated_at"  => $this->updated_at,
            ],

            "relationships" => [
                "requester"  => new UserResource($this->whenLoaded("user")),
                "volunteers" => UserResource::collection($this->whenLoaded("volunteers")),
                "tags"       => TagResource::collection($this->whenLoaded("tags")),
            ],

            "links" => [
                "parent" => route("request.index"),
                "this"   => route("request.show", $this->key)
            ]
        ];
    }
}
