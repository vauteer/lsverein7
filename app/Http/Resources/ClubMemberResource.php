<?php

namespace App\Http\Resources;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubMemberResource extends JsonResource
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
            'range' => $this->range(),
            'memo' => $this->memo,

            'modifiable' => $request->user()->can('update', $this->member),
        ];
    }
}
