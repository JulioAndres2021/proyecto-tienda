<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DatosCheckoutRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'direccion_envio' => ['required', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:100'],
            'codigo_postal' => ['required', 'string', 'max:20'],
            'metodo_pago' => [
                'required',
                'string',
                'in:tarjeta,transferencia,efectivo'
            ],
        ];
    }
}