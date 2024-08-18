<?php

namespace App\Http\Requests;


class StoreRoomRequest extends BaseFormRequest
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
            'sport_id' => 'required|exists:sports,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ];
    }
}