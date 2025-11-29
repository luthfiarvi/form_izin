<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPointQuarter;
use App\Services\GamificationConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GamificationController extends Controller
{
    public function edit(Request $request, GamificationConfigService $config): View
    {
        $cfg = $config->get();

        return view('admin.gamification.settings', [
            'config' => $cfg,
        ]);
    }

    public function update(Request $request, GamificationConfigService $config): RedirectResponse
    {
        $validated = $request->validate([
            'base_penalty_non_sick' => ['required', 'integer', 'min:0', 'max:100'],
            'tolerance_minutes' => ['required', 'integer', 'min:0', 'max:480'],
            'interval_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'penalty_per_interval' => ['required', 'integer', 'min:0', 'max:50'],
            'block_points_at_or_below' => ['required', 'integer', 'min:0', 'max:1000'],
            'block_discipline_at_or_below' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $config->save($validated);

        return back()->with('status', 'Konfigurasi gamification berhasil disimpan.');
    }

    public function summary(Request $request): View
    {
        $year = (int) ($request->input('year') ?: now()->year);
        $month = (int) ($request->input('month') ?: 0);
        $day = (int) ($request->input('day') ?: 0);
        $search = (string) $request->string('q');

        $rows = UserPointQuarter::query()
            ->with('user')
            ->where('year', $year)
            ->when($month > 0, fn($q) => $q->whereMonth('closed_at', $month))
            ->when($day > 0, fn($q) => $q->whereDay('closed_at', $day))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('user_id')
            ->orderBy('quarter')
            ->paginate(30)
            ->withQueryString();

        $availableYears = UserPointQuarter::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->all();

        if (empty($availableYears)) {
            $availableYears = [$year];
        }

        return view('admin.gamification.summary', [
            'rows' => $rows,
            'year' => $year,
            'availableYears' => $availableYears,
            'month' => $month,
            'day' => $day,
            'search' => $search,
        ]);
    }
}
