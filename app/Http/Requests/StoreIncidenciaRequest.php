<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la protección real ya está en el middleware de la ruta
    }

    public function rules(): array
    {
        return [
            'folio' => ['required', 'string', 'max:20'],
            'situacion' => ['required', 'string', 'in:'.implode(',', [
                'Beneficiaria no puede agregar su tarjeta',
                'Error de datos en impresión de acuse',
                'Folio duplicado',
                'Folio no Capturado',
                'No puede descargar la App',
                'Tarjeta dañada / defectuosa',
                'Tarjeta entregada a la beneficiaria pero olvidada en mesa',
                'Tarjeta entregada a otra beneficiaria',
                'Tarjeta no localizada',
                'Tarjeta sin saldo',
                'Otro motivo',
            ])],
            'accion' => ['required', 'string', 'in:'.implode(',', [
                'El caso debe ser redirigido con BROXEL',
                'Entrega de tarjeta nueva',
                'Especificar Motivo o Acción (Folio no capturado)',
                'Reimpresión de acuse',
                'Solicitud de cancelación o reversa en SIGITEL',
                'Otra acción',
            ])],
            'comentarios' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'folio.required' => 'El folio es obligatorio.',
            'situacion.required' => 'Selecciona la situación detectada.',
            'situacion.in' => 'Selecciona una situación válida de la lista.',
            'accion.required' => 'Selecciona la acción a tomar.',
            'accion.in' => 'Selecciona una acción válida de la lista.',
        ];
    }
}
