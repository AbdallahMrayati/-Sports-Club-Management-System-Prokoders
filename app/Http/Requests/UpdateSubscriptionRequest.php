<?php

namespace App\Http\Requests;


class UpdateSubscriptionRequest extends BaseFormRequest
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
            'price' => 'sometimes|required|numeric',
            'type' => 'sometimes|required|string',
            'sport_ids' => 'sometimes|nullable|array',
            'sport_ids.*' => 'exists:sports,id',
            'offer_ids' => 'sometimes|nullable|array',
            'offer_ids.*' => 'exists:offers,id',
        ];
    }
}