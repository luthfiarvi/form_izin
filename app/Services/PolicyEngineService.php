<?php

namespace App\Services;

use App\Exceptions\PolicyViolationException;
use App\Models\User;
use App\Models\PolicyLog;
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
        if (method_exists($user, 'getAttribute')) {
            $status = strtolower((string) $user->getAttribute('status'));
            $isAdmin = (($user->role ?? null) === 'admin') || (bool) ($user->is_kepala_kepegawaian ?? false);
            // Require explicit approval for non-admins
            if (!$isAdmin && $status !== 'active') {
                $reasons[] = 'Akun belum disetujui admin';
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
                    'status' => method_exists($user, 'getAttribute') ? (string) $user->getAttribute('status') : null,
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
