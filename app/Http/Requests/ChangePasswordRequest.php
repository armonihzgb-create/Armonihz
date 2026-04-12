<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // Google-only accounts (no local password) cannot use this form
        if ($user?->google_id && !$user?->password) {
            return false;
        }

        return $user !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',       // at least one lowercase letter
                'regex:/[A-Z]/',       // at least one uppercase letter
                'regex:/[0-9]/',       // at least one digit
                'regex:/[@$!%*#?&]/',  // at least one special character
                'confirmed',
                'different:current_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'        => 'Debes ingresar tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required'                => 'La nueva contraseña es obligatoria.',
            'password.min'                     => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'                   => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.',
            'password.confirmed'               => 'Las contraseñas no coinciden.',
            'password.different'               => 'La nueva contraseña no puede ser igual a la actual.',
        ];
    }
}
