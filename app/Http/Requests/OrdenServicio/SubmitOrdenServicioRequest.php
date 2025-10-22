<?php

namespace App\Http\Requests\OrdenServicio;

use App\Enums\Response\ResponseStatusEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitOrdenServicioRequest extends FormRequest
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
            "costo_estimado"=>"required|min:1",
            "costo_final"=>"required|min:1|",
        ];
    }

    protected function failedValidation(Validator $validator) {
        $message = $validator->errors()->all();
        throw new HttpResponseException(response()->json(['status' => ResponseStatusEnum::ERROR, 'message' => 'Fallo en la validación de datos', 'errors' => $message]));
    }
}
