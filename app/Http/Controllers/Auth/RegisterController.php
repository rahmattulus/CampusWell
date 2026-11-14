<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the 2-step registration form (M-02).
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for students.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Langkah 1: Akun Mahasiswa
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:30', 'unique:users,nim'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Langkah 2: Profil Faktor Risiko Wajib (v2.1)
            'p1_gastric_history' => ['required', 'in:Ya,Tidak,Tidak tahu'],
            'p2_smoking' => ['required', 'in:Tidak,Kadang,Rutin'],
            'p3_nsaid_usage' => ['required', 'in:Tidak,Kadang,Rutin'],
            'p4_chronic_conditions' => ['nullable', 'string', 'max:500'],
            'consent' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'p1_gastric_history.required' => 'Pilih riwayat maag/GERD.',
            'p2_smoking.required' => 'Pilih kebiasaan merokok.',
            'p3_nsaid_usage.required' => 'Pilih pemakaian obat pereda nyeri.',
            'consent.accepted' => 'Persetujuan pemrosesan data kesehatan wajib dicentang.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            // 1. Create student user with pending status
            $newUser = User::create([
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'verification_status' => 'pending',
            ]);

            // 2. Create user profile with consent
            UserProfile::create([
                'user_id' => $newUser->id,
                'p1_gastric_history' => $validated['p1_gastric_history'],
                'p2_smoking' => $validated['p2_smoking'],
                'p3_nsaid_usage' => $validated['p3_nsaid_usage'],
                'p4_chronic_conditions' => $validated['p4_chronic_conditions'] ?? null,
                'consent_at' => now(),
            ]);

            return $newUser;
        });

        // Log the newly registered user in
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Akunmu sedang menunggu verifikasi oleh Admin.');
    }
}
