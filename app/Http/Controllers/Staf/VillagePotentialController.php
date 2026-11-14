<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use App\Models\VillagePotential;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VillagePotentialController extends Controller
{
    /**
     * Tampilan Kelola Potensi Desa
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = VillagePotential::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category && array_key_exists($category, VillagePotential::CATEGORIES)) {
            $query->where('category', $category);
        }

        $potentials = $query->paginate(10)->withQueryString();

        return Inertia::render('Staf/Village/Potentials', [
            'potentials' => $potentials,
            'categories' => VillagePotential::CATEGORIES,
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
        ]);
    }

    /**
     * Tambah Potensi Desa Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(VillagePotential::CATEGORIES))],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('potentials', 'public');
            $validated['photo_path'] = '/storage/'.$path;
        }

        unset($validated['photo']);
        VillagePotential::create($validated);

        return back()->with('success', 'Potensi desa berhasil ditambahkan.');
    }

    /**
     * Perbarui Potensi Desa
     */
    public function update(Request $request, VillagePotential $potential): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(VillagePotential::CATEGORIES))],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($potential->photo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $potential->photo_path))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $potential->photo_path));
            }
            $path = $request->file('photo')->store('potentials', 'public');
            $validated['photo_path'] = '/storage/'.$path;
        }

        unset($validated['photo']);
        $potential->update($validated);

        return back()->with('success', 'Potensi desa berhasil diperbarui.');
    }

    /**
     * Hapus Potensi Desa
     */
    public function destroy(VillagePotential $potential): RedirectResponse
    {
        if ($potential->photo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $potential->photo_path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $potential->photo_path));
        }

        $potential->delete();

        return back()->with('success', 'Potensi desa berhasil dihapus.');
    }
}

