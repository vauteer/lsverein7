<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DebitResource extends JsonResource
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
            'member_name' => $this->member->fullName(),
            'amount' => $this->amount,
            'transfer_text' => $this->transfer_text,
            'due_at' => formatDate($this->due_at),

            'modifiable' => $request->user()->can('update', $this->resource),
        ];
    }
}
