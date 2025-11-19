<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormIzinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'date' => ['nullable','date'],
            'in_time' => ['nullable','date_format:H:i'],
            'out_time' => ['nullable','date_format:H:i'],
            'purpose' => ['nullable','string','max:2000'],
            'izin_type' => ['nullable','string','max:100'],
            // attachment optional
            'attachment' => ['nullable', 'file', 'max:5120', 'mimetypes:application/pdf,image/png,image/jpeg'],
        ];
    }
}
