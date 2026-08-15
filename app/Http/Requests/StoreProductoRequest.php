<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
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
            'sku' => ['required','string','unique:productos,sku'],
            'descripcion' => ['nullable','string','max:200'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'categoria_id' => ['required','exists:categorias,id'],
        ];
    }

    //Mensajes personalizados para cada acción.
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU del producto es obligatorio.',
            'sku.unique' => 'El SKU ingresado ya pertenece a otro producto.',
            'precio.required' => 'El precio del producto es obligatorio.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'categoria_id.required' => 'La categoría del producto es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}