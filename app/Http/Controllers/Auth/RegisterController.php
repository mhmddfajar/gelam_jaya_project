<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Hamlet;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/Register', [
            'hamlets' => Hamlet::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Handle citizen registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $existingResident = Resident::where('nik', $validated['nik'])->first();

            if ($existingResident) {
                // 1. If NIK already linked to another user account
                if ($existingResident->user()->exists()) {
                    throw ValidationException::withMessages([
                        'nik' => 'NIK sudah terdaftar pada akun lain. Hubungi kantor desa.',
                    ]);
                }

                // 2. If NIK exists, check birth date match
                $birthDateFormatted = date('Y-m-d', strtotime($validated['birth_date']));
                $existingBirthDateFormatted = $existingResident->birth_date->format('Y-m-d');

                if ($existingBirthDateFormatted !== $birthDateFormatted) {
                    throw ValidationException::withMessages([
                        'nik' => 'Data tidak cocok dengan data penduduk. Hubungi kantor desa.',
                    ]);
                }

                $resident = $existingResident;
            } else {
                // 3. NIK not in residents table, create new unverified resident
                $resident = Resident::create([
                    'nik' => $validated['nik'],
                    'family_card_no' => $validated['family_card_no'] ?? null,
                    'name' => $validated['name'],
                    'birth_place' => $validated['birth_place'],
                    'birth_date' => $validated['birth_date'],
                    'gender' => $validated['gender'],
                    'religion' => $validated['religion'],
                    'marital_status' => $validated['marital_status'],
                    'occupation' => $validated['occupation'] ?? null,
                    'education' => $validated['education'] ?? null,
                    'citizenship' => $validated['citizenship'] ?? 'WNI',
                    'address' => $validated['address'],
                    'hamlet_id' => $validated['hamlet_id'] ?? null,
                    'rt' => $validated['rt'],
                    'rw' => $validated['rw'],
                    'family_relation' => $validated['family_relation'] ?? null,
                    'is_verified' => false,
                ]);
            }

            // Create citizen user account
            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => Role::WARGA,
                'resident_id' => $resident->id,
                'is_active' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended($user->dashboardRoute())
            ->with('success', 'Pendaftaran akun warga berhasil! Selamat datang di Sistem Layanan Desa Gelam Jaya.');
    }
}

