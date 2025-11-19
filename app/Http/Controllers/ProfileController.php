<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfileSignatureUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $isAdmin = ($user->role ?? null) === 'admin' || (bool) ($user->is_kepala_kepegawaian ?? false);

        if ($isAdmin) {
            $users = \App\Models\User::query()->orderBy('name')->paginate(20)->withQueryString();
            return view('profile.manage', compact('users', 'isAdmin'));
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update basic fields if provided
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            if (!empty($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's digital signature only.
     */
    public function updateSignature(ProfileSignatureUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Handle signature updates (file upload has priority over canvas)
        if ($request->hasFile('signature_file')) {
            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->signature_path = $path;
        } elseif ($request->filled('signature')) {
            $signatureData = $request->string('signature')->toString();
            if (str_starts_with($signatureData, 'data:image')) {
                $commaPos = strpos($signatureData, ',');
                $meta = substr($signatureData, 0, $commaPos ?: 0);
                $data = substr($signatureData, ($commaPos ?: -1) + 1);
                $ext = 'png';
                if (preg_match('/data:image\/(\w+);base64/i', $meta, $m)) {
                    $ext = strtolower($m[1]);
                }
                $binary = base64_decode($data, true);
                if ($binary !== false) {
                    $filename = 'signatures/'.Str::uuid().'.'.$ext;
                    Storage::disk('public')->put($filename, $binary);
                    $user->signature_path = $filename;
                }
            } else {
                $binary = base64_decode($signatureData, true);
                if ($binary !== false) {
                    $filename = 'signatures/'.Str::uuid().'.png';
                    Storage::disk('public')->put($filename, $binary);
                    $user->signature_path = $filename;
                }
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'signature-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
