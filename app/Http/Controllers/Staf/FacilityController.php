<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FacilityController extends Controller
{
    /**
     * Tampilan Kelola Sarana & Prasarana
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $condition = $request->query('condition');

        $query = Facility::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category && array_key_exists($category, Facility::CATEGORIES)) {
            $query->where('category', $category);
        }

        if ($condition && array_key_exists($condition, Facility::CONDITIONS)) {
            $query->where('condition', $condition);
        }

        $facilities = $query->paginate(10)->withQueryString();

        return Inertia::render('Staf/Village/Facilities', [
            'facilities' => $facilities,
            'categories' => Facility::CATEGORIES,
            'conditions' => Facility::CONDITIONS,
            'filters' => [
                'search' => $search,
                'category' => $category,
                'condition' => $condition,
            ],
        ]);
    }

    /**
     * Tambah Sarana Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(Facility::CATEGORIES))],
            'location' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', Rule::in(array_keys(Facility::CONDITIONS))],
            'description' => ['nullable', 'string'],
        ]);

        Facility::create($validated);

        return back()->with('success', 'Sarana/prasarana berhasil ditambahkan.');
    }

    /**
     * Perbarui Sarana
     */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(Facility::CATEGORIES))],
            'location' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', Rule::in(array_keys(Facility::CONDITIONS))],
            'description' => ['nullable', 'string'],
        ]);

        $facility->update($validated);

        return back()->with('success', 'Sarana/prasarana berhasil diperbarui.');
    }

    /**
     * Hapus Sarana
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        return back()->with('success', 'Sarana/prasarana berhasil dihapus.');
    }
}

