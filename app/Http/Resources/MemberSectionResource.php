<?php

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $pivot = $this->pivot;
        $member = Member::find($pivot->member_id);
        return [
            'id' => $pivot->id,
            'name' => $this->name,
            'range' => Member::getRange($pivot->from, $pivot->to),
            'memo' => $pivot->memo,

            'modifiable' => auth()->user()->can('update', $member),
        ];
    }
}
