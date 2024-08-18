<?php

namespace App\Http\Requests;


class StoreMemberRequest extends BaseFormRequest
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
            'email' => 'required|email|unique:members,email',
            'phone_number' => 'nullable|string|max:15',
            'sport_ids' => 'nullable|array',
            'sport_ids.*' => 'exists:sports,id',
            'subscription_ids' => 'nullable|array',
            'subscription_ids.*' => 'exists:subscriptions,id',
            'subscription_data' => 'nullable|array',
            'subscription_data.*.id' => 'required|exists:subscriptions,id',
            'subscription_data.*.start_date' => 'nullable|date',
            'subscription_data.*.end_date' => 'nullable|date',
            'subscription_data.*.suspension_reason' => 'nullable|string',
        ];
    }
}