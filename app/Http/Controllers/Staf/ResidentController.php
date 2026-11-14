<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staf\StoreResidentRequest;
use App\Http\Requests\Staf\UpdateResidentRequest;
use App\Models\Hamlet;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResidentController extends Controller
{
    /**
     * Display listing of residents with search & pagination.
     */
    public function index(Request $request): Response
    {
        $query = Resident::withTrashed(false)
            ->with(['hamlet', 'user'])
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('family_card_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hamlet_id')) {
            $query->where('hamlet_id', $request->input('hamlet_id'));
        }

        if ($request->has('is_verified') && $request->input('is_verified') !== '') {
            $query->where('is_verified', filter_var($request->input('is_verified'), FILTER_VALIDATE_BOOLEAN));
        }

        $residents = $query->paginate(15)->withQueryString();
        $hamlets = Hamlet::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Staf/Residents/Index', [
            'residents' => $residents,
            'hamlets' => $hamlets,
            'filters' => $request->only(['search', 'hamlet_id', 'is_verified']),
        ]);
    }

    /**
     * Store a newly created resident (staff input, is_verified = true).
     */
    public function store(StoreResidentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_verified'] = true; // Staff-created residents are auto-verified
        $data['citizenship'] = $data['citizenship'] ?? 'WNI';

        Resident::create($data);

        return back()->with('success', 'Data penduduk baru berhasil ditambahkan dan terverifikasi.');
    }

    /**
     * Update resident data. NIK cannot be changed.
     */
    public function update(UpdateResidentRequest $request, Resident $resident): RedirectResponse
    {
        $data = $request->validated();

        // Strip NIK from update payload to prevent accidental change
        unset($data['nik']);
        $data['citizenship'] = $data['citizenship'] ?? 'WNI';

        $resident->update($data);

        return back()->with('success', 'Data penduduk berhasil diperbarui.');
    }

    /**
     * Soft-delete a resident (preserve record as per PRD rule A6).
     */
    public function destroy(Resident $resident): RedirectResponse
    {
        // Prevent deletion if resident has an associated user account
        if ($resident->user()->exists()) {
            return back()->with('error', 'Penduduk ini memiliki akun pengguna dan tidak dapat dihapus. Nonaktifkan akun pengguna terlebih dahulu.');
        }

        $resident->delete();

        return back()->with('success', 'Data penduduk berhasil dihapus (soft delete).');
    }

    /**
     * Set is_verified = true for the given resident.
     */
    public function verify(Resident $resident): RedirectResponse
    {
        if ($resident->is_verified) {
            return back()->with('info', 'Data penduduk ini sudah berstatus terverifikasi.');
        }

        $resident->is_verified = true;
        $resident->save();

        return back()->with('success', "Data penduduk {$resident->name} berhasil diverifikasi.");
    }
}

