<?php

namespace App\Http\Requests;

class StoreSportRequest extends BaseFormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'media' => 'nullable|array', // Specify that media is an array
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,wmv|max:10240', // Validate each file
            'day_ids' => 'nullable|array',
            'day_ids.*' => 'exists:days,id',
            'room_ids' => 'nullable|array',
            'room_ids.*' => 'exists:rooms,id',
            'facility_ids' => 'nullable|array',
            'facility_ids.*' => 'exists:facilities,id',
        ];
    }
}
