<?php

namespace App\Observers;

use App\Models\FormIzin;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class FormIzinObserver
{
    public function created(FormIzin $form): void
    {
        // Minimal audit entry; structure can be expanded once schema is finalized
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'form_izin.created',
            'model_type' => FormIzin::class,
            'model_id' => $form->getKey(),
            'meta' => [
                'attachment_path' => $form->attachment_path,
            ],
        ]);
    }
}

