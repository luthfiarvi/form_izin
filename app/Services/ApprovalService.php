<?php

namespace App\Services;

use App\Exceptions\ApprovalException;
use App\Models\AuditLog;
use App\Models\FormIzin;
use App\Models\User;
use App\Notifications\IzinDecided;
use App\Services\GamificationConfigService;
use Carbon\Carbon;
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

            // Apply gamification logic on approve (points + discipline_score)
            if ($action === 'approve') {
                $user = $form->user()->lockForUpdate()->first();
                if ($user) {
                    /** @var GamificationConfigService $configService */
                    $configService = app(GamificationConfigService::class);
                    $config = $configService->get();

                    $basePenaltyNonSick = (int) ($config['base_penalty_non_sick'] ?? 5);
                    $toleranceMinutes = (int) ($config['tolerance_minutes'] ?? 60);
                    $intervalMinutes = max(1, (int) ($config['interval_minutes'] ?? 30));
                    $penaltyPerInterval = (int) ($config['penalty_per_interval'] ?? 2);

                    // --- Existing violation points logic ---
                    $pointsDeduction = 0;

                    // Base deduction by izin type (case-insensitive)
                    $type = strtolower((string) $form->izin_type);
                    $isSakit = $type === 'sakit';
                    $isDinasLuar = in_array($type, ['dinas luar', 'dinas_luar'], true);

                    if (! $isSakit && ! $isDinasLuar) {
                        // Non-sick, non-dinas-luar (mis. pribadi) kena base
                        $pointsDeduction += $basePenaltyNonSick;

                        // Additional deduction for long duration (> 3 hours)
                        if (!empty($form->in_time) && !empty($form->out_time)) {
                            try {
                                $in = Carbon::createFromFormat('H:i', $form->in_time);
                                $out = Carbon::createFromFormat('H:i', $form->out_time);
                                $minutes = $out->diffInMinutes($in);
                                if ($minutes > 180) {
                                    $pointsDeduction += 10;
                                }
                            } catch (\Throwable $e) {
                                Log::warning('Failed to calculate izin duration for points', [
                                    'form_id' => $form->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }

                    if ($pointsDeduction > 0) {
                        $currentPoints = (int) ($user->points ?? 100);
                        $user->points = max(0, $currentPoints - $pointsDeduction);
                    }

                    // --- New discipline_score gamification logic ---
                    $disciplinePenalty = 0;

                    if (! $isDinasLuar) {
                        // 1. Base penalty by izin type
                        if (! $isSakit) {
                            // contoh: pribadi / kepentingan pribadi, dll.
                            $disciplinePenalty += $basePenaltyNonSick;
                        }

                        // 2. Dynamic time-based penalty
                        if (!empty($form->in_time) && !empty($form->out_time)) {
                            try {
                                $in = Carbon::createFromFormat('H:i', $form->in_time);
                                $out = Carbon::createFromFormat('H:i', $form->out_time);
                                $minutes = $out->diffInMinutes($in);

                                if ($minutes > $toleranceMinutes) {
                                    $excess = $minutes - $toleranceMinutes;
                                    $blocks = (int) ceil($excess / $intervalMinutes);
                                    if ($blocks > 0) {
                                        $disciplinePenalty += $blocks * $penaltyPerInterval;
                                    }
                                }
                            } catch (\Throwable $e) {
                                Log::warning('Failed to calculate duration for discipline_score', [
                                    'form_id' => $form->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }

                    if ($disciplinePenalty > 0) {
                        $currentScore = (int) ($user->discipline_score ?? 100);
                        $user->discipline_score = max(0, $currentScore - $disciplinePenalty);
                    }

                    // Persist user changes if any gamification update occurred
                    if ($user->isDirty(['points', 'discipline_score'])) {
                        $user->save();
                    }
                }
            }

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
