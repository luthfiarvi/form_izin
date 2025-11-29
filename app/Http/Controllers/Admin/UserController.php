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

    /**
     * Redirect show requests to the edit page so /admin/users/{id} does not 404.
     */
    public function show(User $user): RedirectResponse
    {
        return redirect()->route('admin.users.edit', ['user' => $user]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function create(): View
    {
        $user = new User();
        return view('admin.users.create', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:user,admin,hr'],
            'is_kepala_kepegawaian' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone' => ['nullable', 'string', 'max:50'],
            'points' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'discipline_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'password' => ['required', 'string', 'min:8'],
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            'signature' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,avif,heic,heif', 'max:4096'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->is_kepala_kepegawaian = $request->boolean('is_kepala_kepegawaian');
        $user->status = $data['status'] ?? 'active';
        $user->whatsapp_phone = $data['whatsapp_phone'] ?? null;
        $user->points = $data['points'] ?? 100;
        $user->discipline_score = $data['discipline_score'] ?? 100;
        $user->password = Hash::make($data['password']);

        // Upload tanda tangan (file atau canvas)
        if ($request->hasFile('signature_file')) {
            $user->signature_path = $request->file('signature_file')->store('signatures', 'public');
        } elseif ($request->filled('signature')) {
            $dataSig = (string) $request->string('signature');
            if (str_starts_with($dataSig, 'data:image')) {
                $commaPos = strpos($dataSig, ',');
                $payload = substr($dataSig, ($commaPos ?: -1) + 1);
                $binary = base64_decode($payload, true);
                if ($binary !== false) {
                    $filename = 'signatures/'.Str::uuid().'.png';
                    Storage::disk('public')->put($filename, $binary);
                    $user->signature_path = $filename;
                }
            }
        }

        // Upload foto profil
        if ($request->hasFile('profile_photo')) {
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Akun baru berhasil dibuat');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role' => ['required', 'in:user,admin,hr'],
            'is_kepala_kepegawaian' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone' => ['nullable', 'string', 'max:50'],
            'points' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'discipline_score' => ['nullable', 'integer', 'min:0', 'max:100'],
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

        if ($request->filled('points')) {
            $user->points = (int) $request->input('points');
        }

        if ($request->filled('discipline_score')) {
            $user->discipline_score = (int) $request->input('discipline_score');
        }

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

        return redirect()->back()->with('status', 'Data pengguna diperbarui');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');
        Gate::authorize('delete-user', $user);

        if (Auth::id() === $user->id) {
            abort(403, 'Cannot delete yourself');
        }

        $user->delete();
        return redirect()->back()->with('status', 'Pengguna berhasil dihapus');
    }

    public function approve(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        // Hanya ubah bila masih pending/tidak aktif
        if (($user->status ?? 'pending') !== 'active') {
            $user->status = 'active';
            // Optional: sekaligus verifikasi email bila belum
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        return redirect()->back()->with('status', 'Pengguna disetujui');
    }
}
