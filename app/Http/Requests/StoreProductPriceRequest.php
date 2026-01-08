<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductPriceRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                // Validar que no exista ya un precio para este producto y divisa
                "unique:product_prices,currency_id,NULL,id,product_id,{$productId}",
            ],
            'price' => 'required|numeric|min:0|max:999999999999.99',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'currency_id.required' => 'La divisa es obligatoria.',
            'currency_id.exists' => 'La divisa seleccionada no existe.',
            'currency_id.unique' => 'Ya existe un precio para este producto en la divisa seleccionada.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio no puede ser negativo.',
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
