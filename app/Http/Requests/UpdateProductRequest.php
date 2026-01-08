<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para la solicitud.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:150|unique:products,name,' . $this->route('product'),
            'description' => 'nullable|string|max:5000',
            'price' => 'sometimes|required|numeric|min:0|max:999999999999.99',
            'currency_id' => 'sometimes|required|integer|exists:currencies,id',
            'tax_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'manufacturing_cost' => 'nullable|numeric|min:0|max:999999999999.99',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio no puede ser negativo.',
            'currency_id.required' => 'La divisa es obligatoria.',
            'currency_id.exists' => 'La divisa seleccionada no existe.',
            'tax_cost.numeric' => 'El costo de impuestos debe ser un valor numérico.',
            'tax_cost.min' => 'El costo de impuestos no puede ser negativo.',
            'manufacturing_cost.numeric' => 'El costo de fabricación debe ser un valor numérico.',
            'manufacturing_cost.min' => 'El costo de fabricación no puede ser negativo.',
        ];
    }

    /**
     * Manejar un intento de validación fallido.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors(),
        ], 422));
    }
}
