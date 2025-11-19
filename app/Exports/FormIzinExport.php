<?php

namespace App\Exports;

use App\Models\FormIzin;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Spatie\SimpleExcel\SimpleExcelWriter;

class FormIzinExport
{
    public static function downloadCsv(Builder $query): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = $query->with(['user', 'decidedBy'])->get()->map(function (FormIzin $f) {
            return [
                'ID' => $f->id,
                'Pemohon' => $f->user?->name,
                'Email' => $f->user?->email,
                'Lampiran' => $f->attachment_path ? basename($f->attachment_path) : '',
                'Approved At' => optional($f->approved_at)->toDateTimeString(),
                'Rejected At' => optional($f->rejected_at)->toDateTimeString(),
                'Decided By' => $f->decidedBy?->name,
                'Created At' => optional($f->created_at)->toDateTimeString(),
            ];
        })->toArray();

        $dir = storage_path('app/tmp');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $file = $dir.'/form_izin_export_'.date('Ymd_His').'.csv';

        $writer = SimpleExcelWriter::create($file)->addRows($rows);
        $writer->close();

        return response()->download($file)->deleteFileAfterSend(true);
    }
}

