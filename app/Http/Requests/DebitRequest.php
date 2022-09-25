<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebitRequest extends FormRequest
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
            'member_id' => 'required|exists:members,id',
            'amount' => 'numeric|min:0',
            'transfer_text' => 'required|string',
            'due_at' => 'required|date'
        ];
    }
}
