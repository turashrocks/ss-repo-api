<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_members' => $this->members->count(),
            'slug' => $this->slug,
            'movies' => MovieResource::collection($this->movies),
            'owner' => new UserResource($this->owner),
            'member' => UserResource::collection($this->members)
        ];
    }
}
