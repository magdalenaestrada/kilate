<?php

namespace App\Http\Requests\OrdenServicio;

use App\Enums\Response\ResponseStatusEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmmitDetalleOrdenServicioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "orden_servicio_id",
            "descripcion",
            "cantidad",
            "precio_unitario",
            "subtotal",
        ];
    }
    protected function failedValidation(Validator $validator) {
        $message = $validator->errors()->all();
        throw new HttpResponseException(response()->json(['status' => ResponseStatusEnum::ERROR, 'message' => 'Fallo en la validación de datos', 'errors' => $message]));
    }
}
