<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
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
             'nombre' => ['required','string','max:255'],
             'descripcion' => ['nullable','string','max:200'],
        ];
    }

    //Mensajes personalizados para cada acción.
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
}
