<?php

namespace App\Http\Requests;


class StoreSubscriptionRequest extends BaseFormRequest
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
            'price' => 'required|numeric',
            'type' => 'required|string',
            'sport_ids' => 'nullable|array',
            'sport_ids.*' => 'exists:sports,id',
            'offer_ids' => 'nullable|array',
            'offer_ids.*' => 'exists:offers,id',
        ];
    }
}