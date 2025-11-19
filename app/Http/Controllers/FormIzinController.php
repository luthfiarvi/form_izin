<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormIzinRequest;
use App\Models\FormIzin;
use App\Services\PolicyEngineService;
use App\Services\ApprovalService;
use App\Notifications\IzinSubmitted;
use App\Exceptions\PolicyViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class FormIzinController extends Controller
{
    public function __construct(private readonly PolicyEngineService $policy, private readonly ApprovalService $approval)
    {
        // Route-level auth middleware is applied in routes/web.php.
        // No controller middleware call needed on Laravel 11+ base Controller.
    }

    public function create(): View
    {
        return view('izin.create');
    }

    public function store(StoreFormIzinRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $this->policy->assertCanSubmit($user);
        } catch (PolicyViolationException $e) {
            \Log::warning('Form Izin submission blocked by policy', [
                'user_id' => $user->id ?? null,
                'reasons' => $e->reasons(),
            ]);
            return back()->withErrors(['policy' => $e->getMessage()])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $form = FormIzin::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'purpose' => $request->purpose,
            'izin_type' => $request->izin_type,
            'attachment_path' => $attachmentPath,
        ]);

        // Notify admins / kepala kepegawaian
        $recipients = \App\Models\User::query()
            ->where('role', 'admin')
            ->orWhere('is_kepala_kepegawaian', true)
            ->get();
        Notification::send($recipients, new IzinSubmitted($form));

        return redirect()->route('dashboard')->with('status', 'Form Izin submitted');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = ($user->role ?? null) === 'admin' || (bool) ($user->is_kepala_kepegawaian ?? false);

        $tanggal = (string) $request->string('tanggal'); // legacy single-date param
        $from = (string) $request->string('from');
        $to = (string) $request->string('to');
        $jenis = (string) $request->string('jenis_izin');

        $q = FormIzin::query()->with('user');
        if (!$isAdmin) {
            $q->where('user_id', $user->id);
        }
        // Flexible date filtering: support range (from/to) as well as legacy single 'tanggal'
        if ($from !== '' && $to !== '') {
            $q->whereBetween('date', [$from, $to]);
        } elseif ($from !== '') {
            $q->whereDate('date', '>=', $from);
        } elseif ($to !== '') {
            $q->whereDate('date', '<=', $to);
        } elseif ($tanggal !== '') {
            $q->whereDate('date', $tanggal);
        }
        if ($jenis !== '') {
            $q->where('izin_type', $jenis);
        }

        $forms = $q->latest()->paginate(15)->withQueryString();

        return view('izin.index', [
            'forms' => $forms,
            'tanggal' => $tanggal,
            'from' => $from,
            'to' => $to,
            'jenis' => $jenis,
            'isAdmin' => $isAdmin,
        ]);
    }

    // Admin-only actions exposed on the /izin/data page
    public function approve(Request $request, FormIzin $formIzin): RedirectResponse
    {
        $this->approval->decide($formIzin, 'approve', (int) $request->user()->id);
        return back()->with('msg', 'Berhasil disetujui');
    }

    public function reject(Request $request, FormIzin $formIzin): RedirectResponse
    {
        $this->approval->decide($formIzin, 'reject', (int) $request->user()->id);
        return back()->with('msg', 'Berhasil ditolak');
    }

    public function destroy(Request $request, FormIzin $formIzin): RedirectResponse
    {
        if ($formIzin->attachment_path) {
            Storage::disk('public')->delete($formIzin->attachment_path);
        }
        $formIzin->delete();
        return back()->with('pesan', 'hapus_sukses');
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $isAdmin = ($user->role ?? null) === 'admin' || (bool) ($user->is_kepala_kepegawaian ?? false);
        abort_unless($isAdmin, 403);

        $tanggal = (string) $request->string('tanggal'); // legacy
        $from = (string) $request->string('from');
        $to = (string) $request->string('to');
        $jenis = (string) $request->string('jenis_izin');

        $q = FormIzin::query()->with('user');
        if ($from !== '' && $to !== '') {
            $q->whereBetween('date', [$from, $to]);
        } elseif ($from !== '') {
            $q->whereDate('date', '>=', $from);
        } elseif ($to !== '') {
            $q->whereDate('date', '<=', $to);
        } elseif ($tanggal !== '') {
            $q->whereDate('date', $tanggal);
        }
        if ($jenis !== '') {
            $q->where('izin_type', $jenis);
        }

        $rows = $q->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="form_izin.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Nama', 'Masuk', 'Keluar', 'Jenis', 'Keperluan', 'Status', 'Waktu Input']);
            foreach ($rows as $r) {
                $status = $r->approved_at ? 'approved' : ($r->rejected_at ? 'rejected' : 'pending');
                fputcsv($out, [
                    optional($r->date)->format('Y-m-d') ?: (string) $r->date,
                    optional($r->user)->name,
                    $r->in_time,
                    $r->out_time,
                    $r->izin_type,
                    $r->purpose,
                    $status,
                    optional($r->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function view(Request $request, FormIzin $formIzin): View
    {
        $user = $request->user();
        $isAdmin = ($user->role ?? null) === 'admin' || (bool) ($user->is_kepala_kepegawaian ?? false);

        if (!$isAdmin) {
            if ($formIzin->user_id !== $user->id) {
                abort(403);
            }
            if (is_null($formIzin->approved_at)) {
                abort(403, 'Form belum disetujui');
            }
        }

        $formIzin->load('user', 'decidedBy');

        return view('izin.show', [
            'form' => $formIzin,
            'isAdmin' => $isAdmin,
        ]);
    }
}
