<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPointQuarter;
use App\Services\GamificationConfigService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
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

        // Fallback: jika belum ada rekap tersimpan, tampilkan snapshot live dari semua user
        if ($rows->isEmpty()) {
            $users = \App\Models\User::all();
            $qNow = (int) ceil(now()->month / 3);
            $data = $users->map(function ($u) use ($year, $qNow) {
                $start = 100;
                $current = (int) ($u->points ?? $start);
                $deduction = max(0, $start - $current);
                $o = new \stdClass();
                $o->user = $u;
                $o->year = $year;
                $o->quarter = $qNow;
                $o->starting_points = $start;
                $o->ending_points = $current;
                $o->total_deduction = $deduction;
                $o->closed_at = null;
                return $o;
            });

            $rows = new LengthAwarePaginator(
                $data,
                $data->count(),
                max(1, $data->count()),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('admin.gamification.summary', [
            'rows' => $rows,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'search' => $search,
        ]);
    }

    public function exportSummary(Request $request)
    {
        $year = (int) ($request->input('year') ?: now()->year);
        $month = (int) ($request->input('month') ?: 0);
        $day = (int) ($request->input('day') ?: 0);
        $search = (string) $request->string('q');

        $query = UserPointQuarter::query()
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
            ->orderBy('quarter');

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $users = \App\Models\User::all();
            $qNow = (int) ceil(now()->month / 3);
            $rows = $users->map(function ($u) use ($year, $qNow) {
                $start = 100;
                $current = (int) ($u->points ?? $start);
                $deduction = max(0, $start - $current);
                $o = new \stdClass();
                $o->user = $u;
                $o->year = $year;
                $o->quarter = $qNow;
                $o->starting_points = $start;
                $o->ending_points = $current;
                $o->total_deduction = $deduction;
                $o->closed_at = null;
                return $o;
            });
        }

        $fileName = 'rekap_poin_'.$year.'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Pengguna', 'Email', 'Tahun', 'Kuartal', 'Poin Awal', 'Poin Akhir (Live)', 'Total Pengurangan (Live)', 'Di-reset Pada']);
            foreach ($rows as $r) {
                $user = $r->user;
                fputcsv($out, [
                    $user?->name ?? '-',
                    $user?->email ?? '-',
                    $r->year,
                    'Q'.$r->quarter,
                    $r->starting_points ?? 0,
                    $r->ending_points ?? 0,
                    $r->total_deduction ?? 0,
                    optional($r->closed_at)->format('Y-m-d H:i') ?? '-',
                ]);
            }
            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }
}
