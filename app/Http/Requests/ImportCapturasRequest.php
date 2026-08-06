<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCapturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ];
    }
}
