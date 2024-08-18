<?php

namespace App\Http\Requests;


class StoreUserRequest extends BaseFormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|unique:users',
            'name' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'roleName' => 'required',
        ];
    }
}