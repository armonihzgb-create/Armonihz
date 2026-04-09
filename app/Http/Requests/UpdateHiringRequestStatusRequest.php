<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHiringRequestStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'musico';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 1. Agregamos todos los estados posibles
            'status' => 'required|in:accepted,rejected,counter_offer,completed',
            
            // 2. Permitimos la contraoferta y exigimos que sea un número SI el estado es counter_offer
            'counter_offer' => 'nullable|numeric|required_if:status,counter_offer',
            
            // 3. Permitimos el mensaje del músico
            'musician_message' => 'nullable|string'
        ];
    }

    /**
     * (Opcional) Mensajes de error personalizados en español
     */
    public function messages(): array
    {
        return [
            'counter_offer.required_if' => 'Debes proponer un precio para enviar una contraoferta.',
            'counter_offer.numeric' => 'El precio debe ser un valor numérico.'
        ];
    }
}