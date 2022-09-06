<?php

namespace App\Http\Resources;

use App\ClubRole;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => ClubRole::from($this->clubRole(currentClubId()))->name,
            'lastLogin' => $this->lastLogin()?->format('d.m.Y H:i'),

            'modifiable' => Auth::user()->admin,
        ];
    }
}
