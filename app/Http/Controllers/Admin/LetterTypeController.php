<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLetterTypeRequest;
use App\Http\Requests\Admin\UpdateLetterTypeRequest;
use App\Models\LetterType;
use App\Models\LetterTypeRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class LetterTypeController extends Controller
{
    /**
     * Display listing of letter types.
     */
    public function index(Request $request): Response
    {
        $query = LetterType::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $letterTypes = $query->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/LetterTypes/Index', [
            'letterTypes' => $letterTypes,
            'filters' => $request->only(['search', 'is_active']),
            'stats' => [
                'total' => LetterType::count(),
                'active' => LetterType::where('is_active', true)->count(),
                'inactive' => LetterType::where('is_active', false)->count(),
            ],
        ]);
    }

    /**
     * Show form to create a new letter type.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/LetterTypes/Create');
    }

    /**
     * Store a newly created letter type in storage.
     */
    public function store(StoreLetterTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        DB::transaction(function () use ($validated) {
            $letterType = LetterType::create($validated);

            if (!empty($validated['requirements'])) {
                foreach ($validated['requirements'] as $reqName) {
                    LetterTypeRequirement::create([
                        'letter_type_id' => $letterType->id,
                        'name' => $reqName,
                        'is_required' => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat baru berhasil ditambahkan.');
    }

    /**
     * Show form to edit the specified letter type.
     */
    public function edit(LetterType $letter_type): Response
    {
        return Inertia::render('Admin/LetterTypes/Edit', [
            'letterType' => $letter_type,
        ]);
    }

    /**
     * Update the specified letter type in storage.
     */
    public function update(UpdateLetterTypeRequest $request, LetterType $letter_type): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        DB::transaction(function () use ($letter_type, $validated) {
            $letter_type->update($validated);

            // Sync requirements table
            $letter_type->typeRequirements()->delete();
            if (!empty($validated['requirements'])) {
                foreach ($validated['requirements'] as $reqName) {
                    LetterTypeRequirement::create([
                        'letter_type_id' => $letter_type->id,
                        'name' => $reqName,
                        'is_required' => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Remove the specified letter type from storage.
     */
    public function destroy(LetterType $letter_type): RedirectResponse
    {
        // Guard against deleting letter types with existing requests
        if (Schema::hasTable('letter_requests')) {
            $hasRequests = DB::table('letter_requests')
                ->where('letter_type_id', $letter_type->id)
                ->exists();

            if ($hasRequests) {
                return back()->with('error', 'Tidak dapat menghapus jenis surat yang sudah memiliki riwayat pengajuan.');
            }
        }

        DB::transaction(function () use ($letter_type) {
            $letter_type->typeRequirements()->delete();
            $letter_type->delete();
        });

        return redirect()->route('admin.letter-types.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(LetterType $letter_type): RedirectResponse
    {
        $letter_type->update([
            'is_active' => !$letter_type->is_active,
        ]);

        $statusMsg = $letter_type->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jenis surat {$letter_type->code} berhasil {$statusMsg}.");
    }
}
