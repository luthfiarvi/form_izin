<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormIzin;
use App\Models\AuditLog;
use App\Notifications\IzinDecided;
use App\Exceptions\ApprovalException;
use App\Services\ApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Exports\FormIzinExport;

class IzinController extends Controller
{
    public function __construct(private readonly ApprovalService $approval)
    {
        // Middleware is applied at route level in routes/web.php
    }

    public function index(Request $request): View
    {
        $status = $request->string('status')->lower()->toString();
        $search = (string) $request->string('q');

        $q = FormIzin::query()->with('user');

        if ($status === 'pending') {
            $q->whereNull('approved_at')->whereNull('rejected_at');
        } elseif ($status === 'approved') {
            $q->whereNotNull('approved_at');
        } elseif ($status === 'rejected') {
            $q->whereNotNull('rejected_at');
        }

        if ($search !== '') {
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            });
        }

        $forms = $q->latest()->paginate(15)->withQueryString();

        return view('admin.izin.index', compact('forms', 'status', 'search'));
    }

    public function show(FormIzin $formIzin): View
    {
        $formIzin->load('user', 'decidedBy');
        return view('admin.izin.show', ['form' => $formIzin]);
    }

    public function update(Request $request, FormIzin $formIzin): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        try {
            $adminId = Auth::id();
            $this->approval->decide($formIzin, $request->action, (int) $adminId);
            return back()->with('status', 'Form updated');
        } catch (ApprovalException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }
    }

    public function destroy(FormIzin $formIzin): RedirectResponse
    {
        if ($formIzin->attachment_path) {
            Storage::disk('public')->delete($formIzin->attachment_path);
        }

        $id = $formIzin->id;
        $formIzin->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'form_izin.deleted',
            'model_type' => FormIzin::class,
            'model_id' => $id,
            'meta' => [],
        ]);

        return redirect()->route('admin.izin.index')->with('status', 'Form deleted');
    }

    public function export(Request $request)
    {
        $status = $request->string('status')->lower()->toString();
        $search = (string) $request->string('q');

        $q = FormIzin::query()->with('user');

        if ($status === 'pending') {
            $q->whereNull('approved_at')->whereNull('rejected_at');
        } elseif ($status === 'approved') {
            $q->whereNotNull('approved_at');
        } elseif ($status === 'rejected') {
            $q->whereNotNull('rejected_at');
        }

        if ($search !== '') {
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            });
        }

        return FormIzinExport::downloadCsv($q);
    }
}
