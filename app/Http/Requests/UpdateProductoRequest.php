<?php

namespace App\Http\Requests;

use App\Rules\ValidSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],

            'sku' => [
                'sometimes',
                'required',
                'string',
                new ValidSku(),
                Rule::unique('productos', 'sku')
                    ->ignore($this->route('producto')->id),
            ],

            'descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:200'
            ],

            'precio' => [
                'sometimes',
                'required',
                'numeric',
                'min:0'
            ],

            'stock' => [
                'sometimes',
                'required',
                'integer',
                'min:0'
            ],

            'categoria_id' => [
                'sometimes',
                'required',
                'exists:categorias,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU del producto es obligatorio.',
            'sku.unique' => 'El SKU ingresado ya pertenece a otro producto.',
            'descripcion.string' => 'La descripción debe ser un texto.',
            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio no puede ser menor a 0.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor a 0.',
            'categoria_id.required' => 'La categoría del producto es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}
