<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MemberResource extends JsonResource
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
            'member_id' => $this->member_id,
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'address' => $this->zipcode . ' ' . $this->city . ' ' . $this->street,
            'subscriptions' => Str::limit($this->currentSubscriptions(), 25),
            'currentRoles' => Str::limit($this->currentRoles(), 25),
            'gender' => $this->gender,
            'sex' => $this->gender->name,
            'birthday' => formatDate($this->birthday),
            'age' => $this->age,
            'gone' => $this->gone(),
            'isMember' => $this->isMember(),
            'membershipYears' => $this->membershipYears(),
            'sections' => Str::limit($this->currentSections(), 25),
            'lastEvent' => Str::limit($this->lastEvent(), 25),

            'modifiable' => $request->user()->can('update', $this->resource),
        ];
    }
}
