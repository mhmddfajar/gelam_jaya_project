<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hamlet;
use App\Models\Official;
use App\Models\VillageProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VillageController extends Controller
{
    /**
     * Tampilan Identitas Desa & Wilayah (Dusun)
     */
    public function identity(): Response
    {
        $profile = VillageProfile::current();
        $hamlets = Hamlet::withCount('residents')->orderBy('name')->get();

        return Inertia::render('Admin/Village/Identity', [
            'profile' => $profile,
            'hamlets' => $hamlets,
        ]);
    }

    /**
     * Simpan Perubahan Identitas Desa
     */
    public function updateIdentity(Request $request): RedirectResponse
    {
        $profile = VillageProfile::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'village_code' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'district' => ['required', 'string', 'max:100'],
            'regency' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'area_size_ha' => ['nullable', 'numeric', 'min:0'],
            'total_population' => ['nullable', 'integer', 'min:0'],
            'total_households' => ['nullable', 'integer', 'min:0'],
            'male_population' => ['nullable', 'integer', 'min:0'],
            'female_population' => ['nullable', 'integer', 'min:0'],
            'boundary_north' => ['nullable', 'string', 'max:150'],
            'boundary_south' => ['nullable', 'string', 'max:150'],
            'boundary_east' => ['nullable', 'string', 'max:150'],
            'boundary_west' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $profile->logo_path))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profile->logo_path));
            }
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = '/storage/'.$path;
        }

        unset($validated['logo']);
        $profile->update($validated);

        return back()->with('success', 'Identitas dan batas wilayah desa berhasil diperbarui.');
    }

    /**
     * Tambah Dusun Baru
     */
    public function storeHamlet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:hamlets,name'],
            'head_name' => ['nullable', 'string', 'max:150'],
        ]);

        Hamlet::create($validated);

        return back()->with('success', 'Data dusun baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Dusun
     */
    public function updateHamlet(Request $request, Hamlet $hamlet): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('hamlets', 'name')->ignore($hamlet->id)],
            'head_name' => ['nullable', 'string', 'max:150'],
        ]);

        $hamlet->update($validated);

        return back()->with('success', 'Data dusun berhasil diperbarui.');
    }

    /**
     * Hapus Dusun
     */
    public function destroyHamlet(Hamlet $hamlet): RedirectResponse
    {
        if ($hamlet->residents()->exists()) {
            return back()->with('error', 'Dusun tidak dapat dihapus karena masih memiliki penduduk tertaut.');
        }

        $hamlet->delete();

        return back()->with('success', 'Dusun berhasil dihapus.');
    }

    /**
     * Tampilan Visi-Misi & Perangkat Desa (Pemerintahan)
     */
    public function government(): Response
    {
        $profile = VillageProfile::current();
        $officials = Official::ordered()->get();

        return Inertia::render('Admin/Village/Government', [
            'profile' => [
                'id' => $profile->id,
                'vision' => $profile->vision,
                'mission' => $profile->mission,
            ],
            'officials' => $officials,
        ]);
    }

    /**
     * Perbarui Visi & Misi Desa
     */
    public function updateVisionMission(Request $request): RedirectResponse
    {
        $profile = VillageProfile::current();

        $validated = $request->validate([
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
        ]);

        $profile->update($validated);

        return back()->with('success', 'Visi dan misi desa berhasil diperbarui.');
    }

    /**
     * Tambah Perangkat Desa Baru
     */
    public function storeOfficial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:100'],
            'period_start' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'period_end' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'can_sign' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $validated['can_sign'] = $request->boolean('can_sign');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('officials', 'public');
            $validated['photo_path'] = '/storage/'.$path;
        }

        unset($validated['photo']);
        Official::create($validated);

        return back()->with('success', 'Perangkat desa baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Perangkat Desa
     */
    public function updateOfficial(Request $request, Official $official): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:100'],
            'period_start' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'period_end' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'can_sign' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $validated['can_sign'] = $request->boolean('can_sign');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $official->is_active;

        if ($request->hasFile('photo')) {
            if ($official->photo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $official->photo_path))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $official->photo_path));
            }
            $path = $request->file('photo')->store('officials', 'public');
            $validated['photo_path'] = '/storage/'.$path;
        }

        unset($validated['photo']);
        $official->update($validated);

        return back()->with('success', 'Data perangkat desa berhasil diperbarui.');
    }

    /**
     * Hapus Perangkat Desa
     */
    public function destroyOfficial(Official $official): RedirectResponse
    {
        if ($official->photo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $official->photo_path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $official->photo_path));
        }

        $official->delete();

        return back()->with('success', 'Perangkat desa berhasil dihapus.');
    }
}

