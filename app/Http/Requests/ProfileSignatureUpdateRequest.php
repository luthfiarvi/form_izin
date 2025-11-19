<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSignatureUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,avif,heic,heif', 'max:4096'],
            'signature' => ['nullable', 'string'],
        ];
    }
}

