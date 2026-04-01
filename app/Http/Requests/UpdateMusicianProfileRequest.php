<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMusicianProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated musicians may update their own profile
        return $this->user()?->role === 'musico';
    }

    public function rules(): array
    {
        return [
            'stage_name'      => 'required|string|max:255',
            'bio'             => 'nullable|string|max:2000',
            'location'        => 'nullable|string|max:255',
            'hourly_rate'     => 'nullable|numeric|min:0',
            'phone'           => [
                'nullable',
                'string',
                'regex:/^\+?[0-9\s\-]*$/',
                function ($attribute, $value, $fail) {
                    if (preg_match_all('/[0-9]/', $value) > 10) {
                        $fail('El número de teléfono no debe tener más de 10 números.');
                    }
                },
            ],
            'instagram'       => 'nullable|string|max:255',
            'facebook'        => 'nullable|string|max:255',
            'youtube'         => 'nullable|string|max:255',
            'coverage_notes'  => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'genres'          => 'nullable|array',
            'genres.*'        => 'integer|exists:genres,id',
            'group_types'     => 'nullable|array',
            'group_types.*'   => 'integer|exists:group_types,id',
            'event_types'     => 'nullable|array',
            'event_types.*'   => 'integer|exists:event_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'stage_name.required'      => 'El nombre artístico es obligatorio.',
            'stage_name.max'           => 'El nombre artístico no puede superar los 255 caracteres.',
            'bio.max'                  => 'La biografía no puede superar los 2000 caracteres.',
            'hourly_rate.numeric'      => 'La tarifa debe ser un número.',
            'hourly_rate.min'          => 'La tarifa no puede ser negativa.',
            'phone.regex'              => 'El teléfono solo puede contener números, espacios, guiones y un símbolo + opcional al inicio.',
            'profile_picture.image'    => 'La foto de perfil debe ser una imagen.',
            'profile_picture.mimes'    => 'La foto debe ser JPG, JPEG, PNG o WebP.',
            'profile_picture.max'      => 'La foto no puede superar los 3MB.',
            'genres.*.exists'          => 'Uno de los géneros seleccionados no es válido.',
        ];
    }
}

