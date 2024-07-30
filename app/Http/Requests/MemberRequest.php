<?php

namespace App\Http\Requests;

use App\Rules\Iban;
use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
            'surname' => 'required|string',
            'first_name' => 'required|string',
            'gender' => 'required',
            'birthday' => 'required|date',
            'death_day' => 'nullable|date',
            'street' => 'string',
            'zipcode' => 'string',
            'city' => 'string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'payment_method' => 'required',
            'bank' => 'nullable|string|required_if:payment_method,k',
            'account_owner' => 'nullable|regex:' . SEPA_REGEX . '|required_if:payment_method,k',
            'iban' => ['nullable', 'required_if:payment_method,k', new Iban()],
            'bic' => 'nullable|regex:' . BIC_REGEX . '|required_if:payment_method,k',
            'memo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'bic.regex' => 'Ungültige BIC',
            'account_owner.regex' => 'Ungültige Sonderzeichen (äöüß&...)',
        ];
    }

}
