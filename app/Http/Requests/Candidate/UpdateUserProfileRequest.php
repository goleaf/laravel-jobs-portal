<?php

namespace App\Http\Requests\Candidate;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the given data was invalid
     */
    public function rules(): array
    {
        $id = \Auth::user()->id;

        return [
            'first_name' => 'required|max:180',
            'last_name' => 'nullable|max:180',
            'email' => 'required|email|unique:users,email,'.$id.'|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
            'phone' => 'nullable',
            'image' => 'nullable|mimes:jpeg,jpg,png',
        ];
    }

    public function messages(): array
    {
        return User::$messages;
    }
}
