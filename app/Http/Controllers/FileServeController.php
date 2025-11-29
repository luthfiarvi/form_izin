<?php

namespace App\Http\Controllers;

use App\Models\FormIzin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileServeController extends Controller
{
    // Route-level middleware 'auth' is applied in routes/web.php

    public function serveAttachment(Request $request, string $filename): StreamedResponse
    {
        $filename = basename($filename);
        $path = 'attachments/'.$filename;

        $form = FormIzin::query()->where('attachment_path', $path)->first();
        if (! $form) {
            abort(404);
        }

        $user = Auth::user();
        $allowed = false;
        if ($user->id === $form->user_id) {
            $allowed = true;
        }
        if (! $allowed && $form->decided_by && $user->id === $form->decided_by) {
            $allowed = true;
        }
        if (! $allowed && Gate::forUser($user)->allows('admin-only')) {
            $allowed = true;
        }

        if (! $allowed) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }

    public function serveSignature(Request $request, string $filename): StreamedResponse
    {
        $filename = basename($filename);
        $path = 'signatures/'.$filename;

        $owner = User::query()->where('signature_path', $path)->first();
        if (! $owner) {
            abort(404);
        }

        $user = Auth::user();
        if ($user->id !== $owner->id && ! Gate::forUser($user)->allows('admin-only')) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // Inline response for images
        return Storage::disk('public')->response($path);
    }

    public function serveHeadSignature(Request $request, \App\Models\FormIzin $formIzin): StreamedResponse
    {
        $user = Auth::user();
        $role = $user->role ?? null;
        $isAdmin = in_array($role, ['admin', 'hr'], true) || (bool) ($user->is_kepala_kepegawaian ?? false);
        if (!$isAdmin && $formIzin->user_id !== $user->id) {
            abort(403);
        }
        if (!$formIzin->approved_at) {
            abort(403);
        }
        $path = $formIzin->head_signature_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }
        return Storage::disk('public')->response($path);
    }
}
