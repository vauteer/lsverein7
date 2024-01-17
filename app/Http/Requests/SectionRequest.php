<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends FormRequest
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
                // don't allow characters that can make proplems within a path
                'regex:/^[a-zA-Z0-9öäüß?()+,\- ]*$/',
                Rule::unique('sections')
                    ->where(fn ($query) => $query->where('club_id', currentClubId()))
                    ->ignore($this->section?->id),
            ],
            'blsv_id' => 'nullable|integer',
        ];
    }
}
