<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PolicyLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $allowedFilter = $request->string('allowed')->toString();

        $q = PolicyLog::query()->with('user')->orderByDesc('evaluated_at');

        if ($search !== '') {
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($allowedFilter === 'yes') {
            // coalesce untuk menangani data lama yang kolom allowed-nya masih null
            $q->whereRaw('coalesce(allowed, false) = true');
        } elseif ($allowedFilter === 'no') {
            $q->whereRaw('coalesce(allowed, false) = false');
        }

        $logs = $q->paginate(25)->withQueryString();

        return view('admin.policy-log.index', [
            'logs' => $logs,
            'search' => $search,
            'allowedFilter' => $allowedFilter,
        ]);
    }
}
