<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warga\UpdateProfileRequest;
use App\Models\Hamlet;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the citizen's own profile / data diri page.
     */
    public function edit(): Response
    {
        $user = request()->user()->load('resident.hamlet');
        $hamlets = Hamlet::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Warga/Profile', [
            'resident' => $user->resident,
            'hamlets' => $hamlets,
        ]);
    }

    /**
     * Update the citizen's own data diri.
     * PRD: warga can only update while is_verified = false; NIK cannot be changed.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user()->load('resident');

        if (!$user->resident) {
            return back()->with('error', 'Data penduduk tidak ditemukan untuk akun ini.');
        }

        // Block update if already verified (staff/admin must do it)
        if ($user->resident->is_verified) {
            return back()->with('error', 'Data diri Anda sudah terverifikasi dan tidak bisa diubah secara mandiri. Hubungi kantor desa untuk perubahan.');
        }

        $data = $request->validated();
        // NIK must never change
        unset($data['nik']);
        $data['citizenship'] = $data['citizenship'] ?? 'WNI';

        $user->resident->update($data);

        // Keep User name in sync with Resident name
        if (isset($data['name']) && $user->name !== $data['name']) {
            $user->update(['name' => $data['name']]);
        }

        return back()->with('success', 'Data diri berhasil diperbarui.');
    }
}

