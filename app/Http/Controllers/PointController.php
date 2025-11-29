<?php

namespace App\Http\Controllers;

use App\Models\FormIzin;
use App\Models\UserPointQuarter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PointController extends Controller
{
    /**
     * Tampilkan ringkasan dan riwayat pengurangan poin pelanggaran user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $year = (int) ($request->input('year') ?: now()->year);

        $forms = FormIzin::query()
            ->where('user_id', $user->id)
            ->whereNotNull('approved_at')
            ->whereYear('approved_at', $year)
            ->orderByDesc('approved_at')
            ->paginate(10)
            ->withQueryString();

        // Hitung detail pemotongan poin per form (tanpa mengubah data di DB)
        $forms->getCollection()->transform(function (FormIzin $form) {
            $typeKey = strtolower((string) $form->izin_type);
            $baseDeduction = $typeKey === 'sakit' ? 0 : 5;

            $durationMinutes = null;
            $durationDeduction = 0;

            if (!empty($form->in_time) && !empty($form->out_time)) {
                try {
                    $in = Carbon::createFromFormat('H:i', $form->in_time);
                    $out = Carbon::createFromFormat('H:i', $form->out_time);
                    $durationMinutes = $out->diffInMinutes($in);
                    if ($durationMinutes > 180) {
                        $durationDeduction = 10;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to calculate izin duration for points history', [
                        'form_id' => $form->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $form->points_base_deduction = $baseDeduction;
            $form->points_duration_deduction = $durationDeduction;
            $form->points_total_deduction = $baseDeduction + $durationDeduction;
            $form->points_duration_minutes = $durationMinutes;

            return $form;
        });

        $currentPoints = (int) ($user->points ?? 100);
        $currentPoints = max(0, $currentPoints);
        $totalDeduction = max(0, 100 - $currentPoints);

        $quarterSummaries = UserPointQuarter::query()
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->orderBy('quarter')
            ->get();

        return view('points.index', [
            'user' => $user,
            'forms' => $forms,
            'currentPoints' => $currentPoints,
            'totalDeduction' => $totalDeduction,
            'year' => $year,
            'quarterSummaries' => $quarterSummaries,
        ]);
    }
}
