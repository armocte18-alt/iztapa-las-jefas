<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CargarStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'], // 20 MB
        ];
    }

    public function messages(): array
    {
        return [
            'archivo_excel.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls).',
            'archivo_excel.max' => 'El archivo no debe pesar más de 20 MB.',
        ];
    }
}
