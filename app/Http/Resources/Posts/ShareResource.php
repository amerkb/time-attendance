<?php

namespace App\Http\Resources\Posts;


use Illuminate\Http\Resources\Json\JsonResource;

class ShareResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'image' => $this->image ? asset($this->image) : null,
                    'position' => $this->user->position,
                ];
            }),
            'post' => PostResource::make($this->whenLoaded('post')),
            'created_at' => $this->created_at ? $this->created_at : null
        ];
    }
}
