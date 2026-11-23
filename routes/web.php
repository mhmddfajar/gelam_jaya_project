<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LetterTypeController as AdminLetterTypeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VillageController as AdminVillageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Staf\FacilityController as StafFacilityController;
use App\Http\Controllers\Staf\RequestController as StafRequestController;
use App\Http\Controllers\Staf\ResidentController as StafResidentController;
use App\Http\Controllers\Staf\VillagePotentialController as StafVillagePotentialController;
use App\Http\Controllers\Warga\ProfileController as WargaProfileController;
use App\Http\Controllers\Warga\RequestController as WargaRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes (Tanpa Login)
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/profil', [PublicController::class, 'profile'])->name('public.profile');
Route::get('/potensi', [PublicController::class, 'potentials'])->name('public.potentials');
Route::get('/sarana', [PublicController::class, 'facilities'])->name('public.facilities');
Route::get('/layanan', [PublicController::class, 'services'])->name('public.services');

// Auth Routes (Guest & Authenticated Account)
require __DIR__.'/auth.php';

// Warga Routes (Middleware: auth, role:warga)
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    // Pengajuan Daring Surat (Fase 5)
    Route::get('/pengajuan', [WargaRequestController::class, 'index'])->name('requests.index');
    Route::get('/pengajuan/baru', [WargaRequestController::class, 'create'])->name('requests.create');
    Route::post('/pengajuan', [WargaRequestController::class, 'store'])->name('requests.store');
    Route::get('/pengajuan/{letter_request}', [WargaRequestController::class, 'show'])->name('requests.show');
    Route::get('/pengajuan/{letter_request}/attachments/{attachment}', [WargaRequestController::class, 'downloadAttachment'])->name('requests.attachments.download');

    // Profil / Data Diri (Fase 2)
    Route::get('/profil', [WargaProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [WargaProfileController::class, 'update'])->name('profile.update');
});

// Staf & Admin Routes (Middleware: auth, role:staf,admin)
Route::middleware(['auth', 'role:staf,admin'])->prefix('staf')->name('staf.')->group(function () {
    Route::get('/pengajuan', [StafRequestController::class, 'index'])->name('requests.index');

    // Data Penduduk CRUD (Fase 2)
    Route::get('/penduduk', [StafResidentController::class, 'index'])->name('residents.index');
    Route::post('/penduduk', [StafResidentController::class, 'store'])->name('residents.store');
    Route::put('/penduduk/{resident}', [StafResidentController::class, 'update'])->name('residents.update');
    Route::delete('/penduduk/{resident}', [StafResidentController::class, 'destroy'])->name('residents.destroy');
    Route::post('/penduduk/{resident}/verify', [StafResidentController::class, 'verify'])->name('residents.verify');

    // Info Desa: Potensi & Sarana CRUD (Fase 3)
    Route::get('/desa/potensi', [StafVillagePotentialController::class, 'index'])->name('village.potentials.index');
    Route::post('/desa/potensi', [StafVillagePotentialController::class, 'store'])->name('village.potentials.store');
    Route::put('/desa/potensi/{potential}', [StafVillagePotentialController::class, 'update'])->name('village.potentials.update');
    Route::delete('/desa/potensi/{potential}', [StafVillagePotentialController::class, 'destroy'])->name('village.potentials.destroy');

    Route::get('/desa/sarana', [StafFacilityController::class, 'index'])->name('village.facilities.index');
    Route::post('/desa/sarana', [StafFacilityController::class, 'store'])->name('village.facilities.store');
    Route::put('/desa/sarana/{facility}', [StafFacilityController::class, 'update'])->name('village.facilities.update');
    Route::delete('/desa/sarana/{facility}', [StafFacilityController::class, 'destroy'])->name('village.facilities.destroy');
});

// Admin Only Routes (Middleware: auth, role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola Pengguna (Fase 1)
    Route::resource('pengguna', AdminUserController::class)->names('users');
    Route::patch('/pengguna/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/pengguna/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // Kelola Master Jenis Surat & Persyaratan (Fase 4)
    Route::resource('jenis-surat', AdminLetterTypeController::class)
        ->names('letter-types')
        ->parameters(['jenis-surat' => 'letter_type']);
    Route::patch('/jenis-surat/{letter_type}/toggle-active', [AdminLetterTypeController::class, 'toggleActive'])->name('letter-types.toggle-active');

    // Info Desa: Identitas & Wilayah (Fase 3)
    Route::get('/desa/identitas-wilayah', [AdminVillageController::class, 'identity'])->name('village.identity');
    Route::put('/desa/identitas-wilayah', [AdminVillageController::class, 'updateIdentity'])->name('village.identity.update');
    Route::post('/desa/dusun', [AdminVillageController::class, 'storeHamlet'])->name('village.hamlets.store');
    Route::put('/desa/dusun/{hamlet}', [AdminVillageController::class, 'updateHamlet'])->name('village.hamlets.update');
    Route::delete('/desa/dusun/{hamlet}', [AdminVillageController::class, 'destroyHamlet'])->name('village.hamlets.destroy');

    // Info Desa: Visi Misi & Perangkat Desa / Pemerintahan (Fase 3)
    Route::get('/desa/pemerintahan', [AdminVillageController::class, 'government'])->name('village.government');
    Route::get('/desa/visi-misi', [AdminVillageController::class, 'government'])->name('village.vision-mission');
    Route::put('/desa/visi-misi', [AdminVillageController::class, 'updateVisionMission'])->name('village.vision-mission.update');
    Route::post('/desa/perangkat', [AdminVillageController::class, 'storeOfficial'])->name('village.officials.store');
    Route::put('/desa/perangkat/{official}', [AdminVillageController::class, 'updateOfficial'])->name('village.officials.update');
    Route::delete('/desa/perangkat/{official}', [AdminVillageController::class, 'destroyOfficial'])->name('village.officials.destroy');
});
