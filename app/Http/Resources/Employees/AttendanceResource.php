<?php

namespace App\Http\Resources\Employees;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{

    public function toArray($request)
    {
        if ($this->status == 0) {
            return [
                'id' => $this->id,
                'date' => $this->date,
                'login_time' => $this->login_time,
                'logout_time' => $this->logout_time,
                'status' => $this->status,
                'user' => $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'image' => $this->user->image ? asset($this->user->image) : null,
                        'position' => $this->user->position,
                    ];
                }),
                'is_justify' => $this->is_justify

            ];
        } else {
            return [
                'id' => $this->id,
                'date' => $this->date,
                'login_time' => $this->login_time,
                'logout_time' => $this->logout_time,
                'status' => $this->status,
                'user' => $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'image' => $this->user->image ? asset($this->user->image) : null,
                        'position' => $this->user->position,
                    ];
                }),

            ];
        }
    }
}
