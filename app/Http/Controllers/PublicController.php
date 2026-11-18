<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Hamlet;
use App\Models\Official;
use App\Models\Resident;
use App\Models\VillagePotential;
use App\Models\VillageProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    /**
     * Landing page (Beranda)
     */
    public function home(): Response|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check()) {
            return redirect(auth()->user()->dashboardRoute());
        }

        $profile = VillageProfile::current();
        $officials = Official::active()->ordered()->take(4)->get();
        $potentials = VillagePotential::latest()->take(3)->get();
        $facilities = Facility::latest()->take(4)->get();

        $stats = [
            'total_residents' => (int) ($profile->total_population ?: 40030),
            'total_households' => (int) ($profile->total_households ?: 11015),
            'total_hamlets' => Hamlet::count() ?: 4,
            'area_size_ha' => (float) ($profile->area_size_ha ?: 294.00),
            'total_potentials' => VillagePotential::count(),
            'total_facilities' => Facility::count(),
        ];

        return Inertia::render('Public/Home', [
            'profile' => $profile,
            'officials' => $officials,
            'potentials' => $potentials,
            'facilities' => $facilities,
            'stats' => $stats,
        ]);
    }

    /**
     * Halaman Profil Desa & Pemerintahan
     */
    public function profile(): Response
    {
        $profile = VillageProfile::current();
        $officials = Official::active()->ordered()->get();
        $hamlets = Hamlet::withCount('residents')->get();

        return Inertia::render('Public/Profile', [
            'profile' => $profile,
            'officials' => $officials,
            'hamlets' => $hamlets,
        ]);
    }

    /**
     * Halaman Potensi Desa
     */
    public function potentials(Request $request): Response
    {
        $selectedCategory = $request->query('category');

        $query = VillagePotential::query()->latest();
        if ($selectedCategory && array_key_exists($selectedCategory, VillagePotential::CATEGORIES)) {
            $query->where('category', $selectedCategory);
        }

        $potentials = $query->paginate(9)->withQueryString();

        return Inertia::render('Public/Potentials', [
            'potentials' => $potentials,
            'categories' => VillagePotential::CATEGORIES,
            'selectedCategory' => $selectedCategory,
            'totalCount' => VillagePotential::count(),
        ]);
    }

    /**
     * Halaman Sarana & Prasarana
     */
    public function facilities(Request $request): Response
    {
        $selectedCategory = $request->query('category');

        $query = Facility::query()->latest();
        if ($selectedCategory && array_key_exists($selectedCategory, Facility::CATEGORIES)) {
            $query->where('category', $selectedCategory);
        }

        $facilities = $query->paginate(12)->withQueryString();

        return Inertia::render('Public/Facilities', [
            'facilities' => $facilities,
            'categories' => Facility::CATEGORIES,
            'conditions' => Facility::CONDITIONS,
            'selectedCategory' => $selectedCategory,
            'totalCount' => Facility::count(),
        ]);
    }

    /**
     * Halaman Informasi Layanan Surat
     */
    public function services(): Response
    {
        $profile = VillageProfile::current();

        return Inertia::render('Public/Services', [
            'profile' => $profile,
        ]);
    }
}

