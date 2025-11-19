<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,avif,heic,heif', 'max:4096'],
            'signature' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            // Foto profil: file gambar umum (ekstensi populer) sampai 4MB
            'profile_photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,avif,heic,heif', 'max:4096'],
        ]);

        $signaturePath = null;
        $profilePhotoPath = null;

        // Prefer uploaded file if provided
        if ($request->hasFile('signature_file')) {
            $signaturePath = $request->file('signature_file')->store('signatures', 'public');
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
                    $signaturePath = $filename;
                }
            } else {
                $binary = base64_decode($signatureData, true);
                if ($binary !== false) {
                    $filename = 'signatures/'.Str::uuid().'.png';
                    Storage::disk('public')->put($filename, $binary);
                    $signaturePath = $filename;
                }
            }
        }

        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $role = strtolower($request->string('email')) === 'luthfiarviandi1@gmail.com' ? 'admin' : 'user';
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'signature_path' => $signaturePath,
            'profile_photo_path' => $profilePhotoPath,
            'whatsapp_phone' => $request->string('whatsapp')->toString() ?: null,
            'role' => $role,
            // New users require approval by default
            'status' => 'pending',
        ]);

        event(new Registered($user));

        // Jangan login otomatis. Arahkan ke halaman login dengan notifikasi
        return redirect()->route('login')->with('status', 'Registrasi berhasil. Akun Anda menunggu persetujuan admin sebelum dapat digunakan.');
    }
}
