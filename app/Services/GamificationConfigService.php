<?php

namespace App\Services;

use App\Models\PolicyRule;

class GamificationConfigService
{
    public const RULE_KEY = 'gamification.discipline';

    /**
     * Nilai default konfigurasi gamification.
     *
     * @return array<string,int>
     */
    public function defaults(): array
    {
        return [
            // Penalti dasar untuk izin non-sakit/non-dinas luar
            'base_penalty_non_sick' => 5,
            // Toleransi menit awal tanpa penalti
            'tolerance_minutes' => 60,
            // Interval menit setelah toleransi
            'interval_minutes' => 30,
            // Penalti per interval
            'penalty_per_interval' => 2,
            // Batas points di mana pengajuan izin diblokir
            'block_points_at_or_below' => 0,
            // Batas discipline_score di mana pengajuan izin diblokir
            'block_discipline_at_or_below' => 50,
        ];
    }

    /**
     * Ambil konfigurasi saat ini (merge dengan default).
     *
     * @return array<string,int>
     */
    public function get(): array
    {
        $defaults = $this->defaults();

        $rule = PolicyRule::where('key', self::RULE_KEY)->first();
        $config = is_array($rule?->config) ? $rule->config : [];

        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $config) || !is_numeric($config[$key])) {
                $config[$key] = $value;
            } else {
                $config[$key] = (int) $config[$key];
            }
        }

        return $config;
    }

    /**
     * Simpan konfigurasi gamification.
     *
     * @param  array<string,mixed>  $input
     */
    public function save(array $input): void
    {
        $defaults = $this->defaults();
        $config = [];

        foreach ($defaults as $key => $value) {
            $config[$key] = isset($input[$key]) && is_numeric($input[$key])
                ? (int) $input[$key]
                : $value;
        }

        PolicyRule::updateOrCreate(
            ['key' => self::RULE_KEY],
            [
                'name' => 'Konfigurasi Gamification Kedisiplinan',
                'description' => 'Parameter dinamis untuk perhitungan poin pelanggaran & skor kedisiplinan.',
                'enabled' => true,
                'config' => $config,
            ]
        );
    }
}

