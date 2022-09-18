<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'address' => $this->zipcode . ' ' . $this->city . ' ' . $this->street,
            'subscriptions' => $this->currentSubscriptions(),
            'roles' => $this->currentRoles(),
            'gender' => $this->gender,
            'sex' => $this->gender->name,
            'birthday' => formatDate($this->birthday),
            'age' => $this->age,
            'gone' => $this->gone(),
            'isMember' => $this->isMember(),
            'membershipYears' => $this->membershipYears(),
            'sections' => $this->currentSections(),
            'lastEvent' => $this->lastEvent(),

            'modifiable' => $request->user()->can('update', $this->resource),
        ];
    }
}
