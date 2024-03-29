<?php

namespace App\Http\Resources;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventMemberResource extends JsonResource
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
            'name' => $this->event->name,
            'date' => formatDate($this->date),
            'memo' => $this->memo,

            'modifiable' => $request->user()->can('update', $this->member),
        ];
    }
}
