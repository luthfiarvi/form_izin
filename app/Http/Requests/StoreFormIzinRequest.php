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
            'date' => ['required','date'],
            'in_time' => ['nullable','date_format:H:i'],
            'out_time' => ['nullable','date_format:H:i'],
            'purpose' => ['required','string','max:2000'],
            'izin_type' => ['required','string','max:100'],
            // Lampiran wajib untuk izin Sakit dan Dinas Luar
            'attachment' => [
                'nullable',
                'required_if:izin_type,Sakit,Dinas Luar',
                'file',
                'max:5120',
                'mimetypes:application/pdf,image/png,image/jpeg',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Hari/Tanggal wajib diisi.',
            'date.date' => 'Format Hari/Tanggal tidak valid.',
            'purpose.required' => 'Keperluan izin wajib diisi.',
            'izin_type.required' => 'Jenis izin wajib dipilih.',
            'attachment.required_if' => 'Lampiran bukti wajib diunggah jika jenis izin Sakit atau Dinas Luar.',
            'attachment.mimetypes' => 'Lampiran harus berupa file PDF atau gambar (JPG/PNG).',
            'attachment.max' => 'Ukuran lampiran maksimal 5MB.',
        ];
    }
}
