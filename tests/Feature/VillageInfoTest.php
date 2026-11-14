<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Facility;
use App\Models\Hamlet;
use App\Models\Official;
use App\Models\Resident;
use App\Models\User;
use App\Models\VillagePotential;
use App\Models\VillageProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageInfoTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected User $wargaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup users per role
        $this->adminUser = User::create([
            'name' => 'Admin Desa',
            'email' => 'admin@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);

        $this->stafUser = User::create([
            'name' => 'Staf Pelayanan',
            'email' => 'staf@gelamjaya.test',
            'role' => Role::STAF,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $resident = Resident::create([
            'nik' => '3603110101900001',
            'name' => 'Warga Gelam',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Mawar No. 1',
            'rt' => '001',
            'rw' => '002',
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $this->wargaUser = User::create([
            'name' => 'Warga Gelam',
            'email' => 'warga@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::WARGA,
            'resident_id' => $resident->id,
            'is_active' => true,
        ]);

        // Seed basic village profile
        VillageProfile::current();
    }

    // 1. Public Routes Tests
    public function test_guest_can_access_public_home_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Home')
            ->has('profile')
            ->has('officials')
            ->has('potentials')
            ->has('facilities')
            ->has('stats')
        );
    }

    public function test_guest_can_access_public_profile_page(): void
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Profile')
            ->has('profile')
            ->has('officials')
            ->has('hamlets')
        );
    }

    public function test_guest_can_access_public_potentials_page_with_filters(): void
    {
        VillagePotential::create([
            'title' => 'Budidaya Lele',
            'category' => 'perikanan',
            'description' => 'Ternak lele bioflok unggulan',
            'location' => 'Dusun 1',
        ]);

        $response = $this->get('/potensi?category=perikanan');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Potentials')
            ->has('potentials.data', 1)
            ->where('selectedCategory', 'perikanan')
        );
    }

    public function test_guest_can_access_public_facilities_page_with_filters(): void
    {
        Facility::create([
            'name' => 'Puskesmas Desa',
            'category' => 'kesehatan',
            'location' => 'Jl. Sehat',
            'quantity' => 1,
            'condition' => 'baik',
            'description' => 'Pelayanan medis dasar',
        ]);

        $response = $this->get('/sarana?category=kesehatan');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Facilities')
            ->has('facilities.data', 1)
            ->where('selectedCategory', 'kesehatan')
        );
    }

    public function test_guest_can_access_public_services_page(): void
    {
        $response = $this->get('/layanan');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/Services')
            ->has('profile')
        );
    }

    // 2. Admin Village Identity & Hamlet Tests
    public function test_admin_can_view_and_update_village_identity(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/desa/identitas-wilayah');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Village/Identity')
            ->has('profile')
            ->has('hamlets')
        );

        $updateResponse = $this->actingAs($this->adminUser)->put('/admin/desa/identitas-wilayah', [
            'name' => 'Desa Gelam Jaya Hebat',
            'district' => 'Pasar Kemis',
            'regency' => 'Tangerang',
            'province' => 'Banten',
            'area_size_ha' => 475.25,
            'boundary_north' => 'Batas Baru Utara',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('village_profiles', [
            'name' => 'Desa Gelam Jaya Hebat',
            'boundary_north' => 'Batas Baru Utara',
        ]);
    }

    public function test_admin_can_crud_hamlets(): void
    {
        // Create Hamlet
        $createResponse = $this->actingAs($this->adminUser)->post('/admin/desa/dusun', [
            'name' => 'Dusun Cempaka',
            'head_name' => 'Bapak Joko',
        ]);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun Cempaka']);

        $hamlet = Hamlet::where('name', 'Dusun Cempaka')->first();

        // Update Hamlet
        $updateResponse = $this->actingAs($this->adminUser)->put("/admin/desa/dusun/{$hamlet->id}", [
            'name' => 'Dusun Cempaka Indah',
            'head_name' => 'Bapak Joko Santoso',
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun Cempaka Indah']);

        // Delete Hamlet (without residents)
        $deleteResponse = $this->actingAs($this->adminUser)->delete("/admin/desa/dusun/{$hamlet->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('hamlets', ['id' => $hamlet->id]);
    }

    public function test_admin_cannot_delete_hamlet_with_linked_residents(): void
    {
        $hamlet = Hamlet::create(['name' => 'Dusun Mawar']);
        Resident::create([
            'nik' => '3603110202900002',
            'name' => 'Penduduk Dusun Mawar',
            'birth_place' => 'Tangerang',
            'birth_date' => '1992-02-02',
            'gender' => 'P',
            'religion' => 'Islam',
            'marital_status' => 'Belum Kawin',
            'address' => 'Dusun Mawar RT 01',
            'rt' => '001',
            'rw' => '001',
            'hamlet_id' => $hamlet->id,
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/desa/dusun/{$hamlet->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('hamlets', ['id' => $hamlet->id]);
    }

    public function test_staf_gets_403_when_accessing_village_identity_or_dusun_routes(): void
    {
        $responseGet = $this->actingAs($this->stafUser)->get('/admin/desa/identitas-wilayah');
        $responseGet->assertStatus(403);

        $responsePut = $this->actingAs($this->stafUser)->put('/admin/desa/identitas-wilayah', [
            'name' => 'Hacked Name',
            'district' => 'Pasar Kemis',
            'regency' => 'Tangerang',
            'province' => 'Banten',
        ]);
        $responsePut->assertStatus(403);

        $responseDusun = $this->actingAs($this->stafUser)->post('/admin/desa/dusun', [
            'name' => 'Dusun Ilegal',
        ]);
        $responseDusun->assertStatus(403);
    }

    // 3. Admin Government & Officials Tests
    public function test_admin_can_view_and_update_vision_mission(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/desa/pemerintahan');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Village/Government')
            ->has('profile')
            ->has('officials')
        );

        $updateResponse = $this->actingAs($this->adminUser)->put('/admin/desa/visi-misi', [
            'vision' => 'Visi Baru Desa Gelam Jaya',
            'mission' => "1. Misi Satu\n2. Misi Dua",
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('village_profiles', [
            'vision' => 'Visi Baru Desa Gelam Jaya',
        ]);
    }

    public function test_admin_can_crud_officials(): void
    {
        // Create Official
        $createResponse = $this->actingAs($this->adminUser)->post('/admin/desa/perangkat', [
            'name' => 'Drs. H. Mulyadi',
            'position' => 'Kepala Desa',
            'period_start' => 2021,
            'period_end' => 2027,
            'can_sign' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('officials', [
            'name' => 'Drs. H. Mulyadi',
            'can_sign' => true,
        ]);

        $official = Official::where('name', 'Drs. H. Mulyadi')->first();

        // Update Official
        $updateResponse = $this->actingAs($this->adminUser)->put("/admin/desa/perangkat/{$official->id}", [
            'name' => 'Drs. H. Mulyadi, M.Si',
            'position' => 'Kepala Desa',
            'period_start' => 2021,
            'period_end' => 2027,
            'can_sign' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('officials', [
            'name' => 'Drs. H. Mulyadi, M.Si',
        ]);

        // Delete Official
        $deleteResponse = $this->actingAs($this->adminUser)->delete("/admin/desa/perangkat/{$official->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('officials', ['id' => $official->id]);
    }

    public function test_staf_gets_403_when_modifying_vision_mission_or_officials(): void
    {
        $responseVision = $this->actingAs($this->stafUser)->put('/admin/desa/visi-misi', [
            'vision' => 'Visi Diubah Staf',
        ]);
        $responseVision->assertStatus(403);

        $responseOfficial = $this->actingAs($this->stafUser)->post('/admin/desa/perangkat', [
            'name' => 'Perangkat Baru',
            'position' => 'Kaur',
        ]);
        $responseOfficial->assertStatus(403);
    }

    // 4. Staf & Admin Village Potential Tests
    public function test_staf_and_admin_can_crud_potentials(): void
    {
        // Staf create potential
        $createResponse = $this->actingAs($this->stafUser)->post('/staf/desa/potensi', [
            'title' => 'Kerajinan Eceng Gondok',
            'category' => 'umkm',
            'location' => 'Dusun Melati',
            'description' => 'Produksi tas dan hiasan rumah',
        ]);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('village_potentials', ['title' => 'Kerajinan Eceng Gondok']);

        $potential = VillagePotential::where('title', 'Kerajinan Eceng Gondok')->first();

        // Staf update potential
        $updateResponse = $this->actingAs($this->stafUser)->put("/staf/desa/potensi/{$potential->id}", [
            'title' => 'Kerajinan Eceng Gondok Modern',
            'category' => 'umkm',
            'location' => 'Dusun Melati RT 01',
            'description' => 'Produksi tas ekspor dan hiasan rumah',
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('village_potentials', ['title' => 'Kerajinan Eceng Gondok Modern']);

        // Admin can also delete potential
        $deleteResponse = $this->actingAs($this->adminUser)->delete("/staf/desa/potensi/{$potential->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('village_potentials', ['id' => $potential->id]);
    }

    // 5. Staf & Admin Facility Tests
    public function test_staf_and_admin_can_crud_facilities(): void
    {
        // Staf create facility
        $createResponse = $this->actingAs($this->stafUser)->post('/staf/desa/sarana', [
            'name' => 'Posyandu Mawar Sehat',
            'category' => 'kesehatan',
            'location' => 'Dusun Kenanga',
            'quantity' => 1,
            'condition' => 'baik',
            'description' => 'Layanan balita dan lansia',
        ]);
        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('facilities', ['name' => 'Posyandu Mawar Sehat']);

        $facility = Facility::where('name', 'Posyandu Mawar Sehat')->first();

        // Admin update facility
        $updateResponse = $this->actingAs($this->adminUser)->put("/staf/desa/sarana/{$facility->id}", [
            'name' => 'Posyandu Mawar Sehat Prima',
            'category' => 'kesehatan',
            'location' => 'Dusun Kenanga RT 03',
            'quantity' => 1,
            'condition' => 'baik',
            'description' => 'Layanan balita, ibu hamil, dan lansia',
        ]);
        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('facilities', ['name' => 'Posyandu Mawar Sehat Prima']);

        // Staf delete facility
        $deleteResponse = $this->actingAs($this->stafUser)->delete("/staf/desa/sarana/{$facility->id}");
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('facilities', ['id' => $facility->id]);
    }

    public function test_warga_cannot_access_staf_village_routes(): void
    {
        $responsePotential = $this->actingAs($this->wargaUser)->get('/staf/desa/potensi');
        $responsePotential->assertStatus(403);

        $responseFacility = $this->actingAs($this->wargaUser)->get('/staf/desa/sarana');
        $responseFacility->assertStatus(403);
    }

    public function test_real_gelam_jaya_seeders_populate_correct_data(): void
    {
        $this->seed(\Database\Seeders\VillageProfileSeeder::class);
        $this->seed(\Database\Seeders\HamletSeeder::class);
        $this->seed(\Database\Seeders\OfficialSeeder::class);
        $this->seed(\Database\Seeders\FacilitySeeder::class);

        // 1. Village Profile Check
        $this->assertDatabaseHas('village_profiles', [
            'name' => 'Gelam Jaya',
            'district' => 'Pasar Kemis',
            'regency' => 'Kabupaten Tangerang',
            'province' => 'Banten',
            'postal_code' => '15560',
            'total_population' => 40030,
            'total_households' => 11015,
            'male_population' => 20218,
            'female_population' => 19812,
            'boundary_north' => 'Kel. Kutabumi',
            'boundary_east' => 'Kel. Priuk',
            'boundary_south' => 'Kel. Alamjaya',
            'boundary_west' => 'Kel. Kutajaya',
        ]);

        // 2. Hamlets Check
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun I', 'head_name' => 'Janawi']);
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun II', 'head_name' => 'Askol']);
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun III', 'head_name' => 'Roni Lesmana']);
        $this->assertDatabaseHas('hamlets', ['name' => 'Dusun IV', 'head_name' => 'Juhri']);

        // 3. Officials Check
        $this->assertDatabaseCount('officials', 12);
        $this->assertDatabaseHas('officials', [
            'name' => 'Moch. Sanusi, AR',
            'position' => 'Kepala Desa',
            'can_sign' => true,
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('officials', [
            'name' => 'Ahmad Beben Bahrowi',
            'position' => 'Sekretaris Desa',
            'can_sign' => true,
            'sort_order' => 2,
        ]);
        $this->assertDatabaseHas('officials', [
            'name' => 'Juhri',
            'position' => 'Kepala Dusun IV',
            'can_sign' => false,
            'sort_order' => 12,
        ]);

        // 4. Facilities Check
        $this->assertDatabaseHas('facilities', ['name' => 'SDN 1 Gelam Jaya', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'SMP N 3 Pasar Kemis', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'SMP Islam Bunaya', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'SMP/SMA Tarsisius', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'SMP/SMA Penabur', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'SMA/SMK Nusa Bangsa', 'category' => 'pendidikan']);
        $this->assertDatabaseHas('facilities', ['name' => 'Posyandu Mawar 1', 'category' => 'kesehatan']);
        $this->assertDatabaseHas('facilities', ['name' => 'Posyandu Mawar 23', 'category' => 'kesehatan']);
        $this->assertDatabaseHas('facilities', ['name' => 'Kantor Desa Gelam Jaya', 'category' => 'fasilitas_umum']);
    }
}

