<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHiringRequestRequest extends FormRequest
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
            'musician_profile_id' => 'required|exists:musician_profiles,id',
            'event_date' => 'required|date|after_or_equal:today',
            'event_location' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'end_time' => 'required|date|after:event_date',
        ];
    }
}
