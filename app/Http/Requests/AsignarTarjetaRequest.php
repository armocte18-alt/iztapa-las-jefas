<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignarTarjetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folio' => ['required', 'string', 'max:20'],
            'nueva_cuenta' => ['required', 'string', 'digits:10'],
            'nueva_tarjeta' => ['required', 'string', 'size:4'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo_electronico' => ['required', 'email'],
            'motivo_reasignacion' => [
                'required', 'string',
                'in:Tarjeta entregada a otra beneficiaria,Tarjeta no localizada,Tarjeta dañada',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nueva_cuenta.digits' => 'La nueva cuenta debe tener exactamente 10 dígitos.',
            'nueva_tarjeta.size' => 'Captura solo los últimos 4 dígitos de la tarjeta.',
            'motivo_reasignacion.in' => 'Selecciona un motivo válido de la lista.',
        ];
    }
}
