<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadIdDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user    = $this->user();
        $profile = $user?->musicianProfile;

        // Only musicians whose verification is not already approved or pending
        return $user?->role === 'musico'
            && $profile !== null
            && !in_array($profile->verification_status, ['approved', 'pending'], true);
    }

    public function rules(): array
    {
        return [
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5 MB
        ];
    }

    public function messages(): array
    {
        return [
            'id_document.required' => 'Debes seleccionar un archivo.',
            'id_document.mimes'    => 'El archivo debe ser JPG, PNG o PDF.',
            'id_document.max'      => 'El archivo no debe exceder los 5MB.',
        ];
    }
}
