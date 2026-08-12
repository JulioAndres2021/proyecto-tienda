<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
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
            'sku' => [
                'required',
                'string',
                Rule::unique('productos', 'sku')->ignore($this->route('id')),
            ],
            'descripcion' => ['nullable','string','max:200'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'categoria_id' => ['required','exists:categorias,id'],
        ];
    }
}
