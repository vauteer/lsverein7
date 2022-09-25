<?php

namespace App\Http\Requests;

use App\Rules\Iban;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubRequest extends FormRequest
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
                Rule::unique('clubs')->ignore($this->club?->id),
            ],
            'street' => 'required|string',
            'zipcode' => 'required|string',
            'city' => 'required|string',
            'bank' => 'required|string',
            'account_owner' => 'required|string',
            'iban' => ['required', new Iban()],
            'bic' => 'required|string',
            'sepa' => 'nullable|string',
            'sepa_date' => 'nullable|date',
            'logo' => 'nullable|string',
            'display' => 'required|int',
            'blsv_member' => 'boolean',
            'use_items' => 'boolean',
            'honor_years' => 'nullable|regex:/^\d{1,2}(,\d{1,2})*$/'
        ];
    }
}
