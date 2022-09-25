<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberSectionRequest extends FormRequest
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
            'section_id' => 'exists:App\Models\Section,id',
            'from' => 'required|date',
            'to' => 'nullable|date|after:from',
            'memo' => 'nullable|string',
        ];
    }
}
