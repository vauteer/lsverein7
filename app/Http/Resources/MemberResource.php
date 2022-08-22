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
            'sections' => 'Sparte',
            'subscriptions' => 'Standard',
            'events' => '5 Jahre',
            'gender' => $this->gender,
            'sex' => $this->gender->name,
            'birthday' => $this->birthday->format('d.m.Y'),
            'age' => $this->age,

            'modifiable' => auth()->user()->can('update', $this->resource),
        ];
    }
}
