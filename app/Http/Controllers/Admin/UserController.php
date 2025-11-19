<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level in routes/web.php
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $q = User::query();
        if ($search !== '') {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        }
        $users = $q->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users', 'search'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role' => ['required', 'in:user,admin'],
            'is_kepala_kepegawaian' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            'signature' => ['nullable', 'string'],
            // Foto profil: file gambar umum (ekstensi populer) sampai 4MB
            'profile_photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,avif,heic,heif', 'max:4096'],
        ]);

        $user->name = $request->string('name')->toString();
        $user->email = $request->string('email')->toString();
        $user->role = $request->string('role')->toString();
        $user->is_kepala_kepegawaian = $request->boolean('is_kepala_kepegawaian');
        $user->status = $request->string('status')->toString();
        $user->whatsapp_phone = $request->string('whatsapp_phone')->toString();

        if ($request->filled('password')) {
            $user->password = Hash::make($request->string('password')->toString());
        }

        // Update signature via file upload
        if ($request->hasFile('signature_file')) {
            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->signature_path = $path;
        } elseif ($request->filled('signature')) {
            $data = (string) $request->string('signature');
            if (str_starts_with($data, 'data:image')) {
                $commaPos = strpos($data, ',');
                $payload = substr($data, ($commaPos ?: -1) + 1);
                $binary = base64_decode($payload, true);
                if ($binary !== false) {
                    $filename = 'signatures/'.Str::uuid().'.png';
                    Storage::disk('public')->put($filename, $binary);
                    $user->signature_path = $filename;
                }
            }
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            if (!empty($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('delete-user', $user);

        if (Auth::id() === $user->id) {
            abort(403, 'Cannot delete yourself');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted');
    }

    public function approve(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        $user->status = 'active';
        $user->save();
        return back()->with('status', 'User approved');
    }
}
