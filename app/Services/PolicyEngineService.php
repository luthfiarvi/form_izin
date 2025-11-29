<?php

namespace App\Services;

use App\Exceptions\PolicyViolationException;
use App\Models\User;
use App\Models\PolicyLog;
use App\Services\GamificationConfigService;
use Illuminate\Support\Facades\Log;

class PolicyEngineService
{
    /**
     * Check whether the current user can submit a Form Izin.
     * In lieu of legacy includes/policy_engine.php, implement a simple, extensible check.
     *
     * @return array{allowed:bool,reasons:array<int,string>}
     */
    public function checkPolicies(User $user): array
    {
        $reasons = [];

        // Example basic checks; extend when legacy rules are available.
        $status = null;
        $points = null;
        $disciplineScore = null;

        /** @var GamificationConfigService $configService */
        $configService = app(GamificationConfigService::class);
        $cfg = $configService->get();
        $blockPointsAt = (int) ($cfg['block_points_at_or_below'] ?? 0);
        $blockDisciplineAt = (int) ($cfg['block_discipline_at_or_below'] ?? 50);

        if (method_exists($user, 'getAttribute')) {
            $status = strtolower((string) $user->getAttribute('status'));
            $points = $user->getAttribute('points');
            $disciplineScore = $user->getAttribute('discipline_score');

            $role = $user->role ?? null;
            $isAdmin = in_array($role, ['admin', 'hr'], true) || (bool) ($user->is_kepala_kepegawaian ?? false);
            // Require explicit approval for non-admins
            if (! $isAdmin && $status !== 'active') {
                $reasons[] = 'Akun belum disetujui admin';
            }

            if (! $isAdmin) {
                // Policy: user must still have positive violation points
                $numericPoints = (int) ($points ?? 100);
                if ($numericPoints <= $blockPointsAt) {
                    $reasons[] = 'Sisa poin pelanggaran Anda habis (0). Hubungi BK.';
                }

                // Game Over rule based on discipline_score
                $numericDiscipline = (int) ($disciplineScore ?? 100);
                if ($numericDiscipline <= $blockDisciplineAt) {
                    $reasons[] = "Skor kedisiplinan Anda Kritis ({$numericDiscipline}/100). Akses izin dibekukan. Harap segera menghadap HRD.";
                }
            }
        }

        $allowed = count($reasons) === 0;

        Log::info('Policy check evaluated', [
            'user_id' => $user->id ?? null,
            'allowed' => $allowed,
            'reasons' => $reasons,
        ]);

        // Persist to policy_log for audit
        try {
            PolicyLog::create([
                'user_id' => $user->id ?? null,
                'policy_key' => 'form_izin.submit',
                'allowed' => $allowed,
                'reasons' => $reasons,
                'context' => [
                    'status' => $status,
                    'points' => $points,
                    'discipline_score' => $disciplineScore,
                ],
                'evaluated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to write policy_log', [
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'allowed' => $allowed,
            'reasons' => $reasons,
        ];
    }

    /**
     * Assert that the current user passes all submission policies.
     * Throws an exception with reasons when denied.
     */
    public function assertCanSubmit(User $user): void
    {
        $result = $this->checkPolicies($user);
        if (! $result['allowed']) {
            Log::warning('Policy violation on Form Izin submission', [
                'user_id' => $user->id ?? null,
                'reasons' => $result['reasons'],
            ]);
            throw new PolicyViolationException($result['reasons']);
        }
    }
}
