<?php

namespace App\Services;

use App\Exceptions\ApprovalException;
use App\Models\AuditLog;
use App\Models\FormIzin;
use App\Models\User;
use App\Notifications\IzinDecided;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApprovalService
{
    /**
     * Decide a Form Izin (approve/reject) with transactional safety and logging.
     *
     * @throws ApprovalException
     */
    public function decide(FormIzin $form, string $action, int $adminId): void
    {
        if (!in_array($action, ['approve', 'reject'], true)) {
            throw new ApprovalException('Invalid action');
        }

        if ($form->approved_at || $form->rejected_at) {
            throw new ApprovalException('Form Izin already decided');
        }

        DB::transaction(function () use ($form, $action, $adminId) {
            if ($action === 'approve') {
                $form->approved_at = now();
                $form->rejected_at = null;
            } else {
                $form->rejected_at = now();
                $form->approved_at = null;
            }
            $form->decided_by = $adminId;
            // Auto-fill Kepala Kepegawaian signature snapshot on approval
            if ($action === 'approve') {
                $kepala = User::query()->where('is_kepala_kepegawaian', true)
                    ->whereNotNull('signature_path')->first();
                if (!$kepala) {
                    $admin = User::find($adminId);
                    if ($admin && $admin->is_kepala_kepegawaian && $admin->signature_path) {
                        $kepala = $admin;
                    }
                }
                if ($kepala && $kepala->signature_path) {
                    $form->head_signature_path = $kepala->signature_path;
                } else {
                    // Fallback to a configured default asset if present
                    $fallback = (string) config('app.head_signature_path', 'signatures/kepala_kepegawaian.png');
                    if ($fallback !== '' && Storage::disk('public')->exists($fallback)) {
                        $form->head_signature_path = $fallback;
                    }
                }
            }
            $form->save();

            $form->load('user');
            if ($form->user) {
                $form->user->notify(new IzinDecided($form));
            }

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'form_izin.'.$action,
                'model_type' => FormIzin::class,
                'model_id' => $form->id,
                'meta' => [],
            ]);
        });

        Log::info('Form Izin decision committed', [
            'form_id' => $form->id,
            'decided_by' => $adminId,
            'action' => $action,
        ]);
    }
}
