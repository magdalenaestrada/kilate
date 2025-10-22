<?php

namespace App\Http\Requests\Credito;

use App\Enums\Response\ResponseStatusEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class SubmitPagoCreditoRequest extends FormRequest
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
            "credito_id"=>"required|exists:pagos_creditos,id",
            "fecha_pago"=>"required",
            "fecha_cancelado"=>"required",
        ];
    }
    protected function failedValidation(Validator $validator) {
        $message = $validator->errors()->all();
        throw new HttpResponseException(response()->json(['status' => ResponseStatusEnum::ERROR, 'message' => 'Fallo en la validación de datos', 'errors' => $message]));
    }
}
