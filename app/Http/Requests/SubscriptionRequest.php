<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('subscriptions')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($this->subscription?->id),
            ],
            'amount' => 'numeric|min:0',
            'transfer_text' => 'required|regex:/^[a-zA-Z0-9:()+<>, \/\.\-]*$/',
            'memo' => 'nullable|string',
        ];
    }
}
