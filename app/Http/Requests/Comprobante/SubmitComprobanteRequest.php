<?php

namespace App\Http\Requests\Comprobante;

use Illuminate\Foundation\Http\FormRequest;

class SubmitComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_cancelacion' => 'required|date',
            'fecha_emision' => 'required|date|before_or_equal:today',
            'comprobante_correlativo' => [
                'required',
                'regex:/^[A-Z]{1}\d{3}-\d{1,8}$/', 
                'max:13',
            ],
            'tipopago' => 'required|string|in:CONTADO,CREDITO,A CUENTA',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_cancelacion.required' => 'La fecha de cancelación es obligatoria.',
            'fecha_emision.before_or_equal' => 'La fecha de emisión no puede ser futura.',
            'comprobante_correlativo.regex' => 'El correlativo debe tener el formato correcto (por ejemplo: F001-00012345).',
            'tipopago.in' => 'El tipo de pago debe ser CONTADO, CRÉDITO o A CUENTA.',
        ];
    }
}
