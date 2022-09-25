<?php

namespace App\Http\Requests;

use App\Rules\UniqueUser;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        // $this->user() is not the same as $this->user !
        return [
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                new UniqueUser($this->user?->id)
            ],
        ];
    }
}
