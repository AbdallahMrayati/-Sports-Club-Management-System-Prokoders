<?php

namespace App\Http\Requests;


class UpdateFacilityRequest extends BaseFormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:1000',
            'type' => 'sometimes|required|string|max:50',
            'sport_ids' => 'sometimes|required|array',
            'sport_ids.*' => 'exists:sports,id',
        ];
    }
}