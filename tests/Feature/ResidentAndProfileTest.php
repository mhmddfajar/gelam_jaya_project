<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Hamlet;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ResidentAndProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected User $wargaUser;
    protected Resident $wargaResident;
    protected Hamlet $hamlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hamlet = Hamlet::create([
            'name' => 'Dusun I Gelam',
            'head_name' => 'Sutrisno',
        ]);

        $this->adminUser = User::create([
            'name' => 'Administrator Desa',
            'email' => 'admin@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);

        $this->stafUser = User::create([
            'name' => 'Staf Pelayanan Desa',
            'email' => 'staf@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::STAF,
            'is_active' => true,
        ]);

        $this->wargaResident = Resident::create([
            'nik' => '3271010101800001',
            'family_card_no' => '3271010101800101',
            'name' => 'Agus Budiman',
            'birth_place' => 'Tangerang',
            'birth_date' => '1980-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam No. 1',
            'rt' => '001',
            'rw' => '001',
            'hamlet_id' => $this->hamlet->id,
            'is_verified' => false,
        ]);

        $this->wargaUser = User::create([
            'name' => $this->wargaResident->name,
            'email' => 'warga@gelamjaya.test',
            'password' => Hash::make('password'),
            'role' => Role::WARGA,
            'resident_id' => $this->wargaResident->id,
            'is_active' => true,
        ]);
    }

    public function test_staf_and_admin_can_view_residents_index(): void
    {
        $responseStaf = $this->actingAs($this->stafUser)->get('/staf/penduduk');
        $responseStaf->assertStatus(200);
        $responseStaf->assertInertia(fn (Assert $page) => $page->component('Staf/Residents/Index'));

        $responseAdmin = $this->actingAs($this->adminUser)->get('/staf/penduduk');
        $responseAdmin->assertStatus(200);
    }

    public function test_warga_cannot_access_staf_residents_index(): void
    {
        $response = $this->actingAs($this->wargaUser)->get('/staf/penduduk');
        $response->assertStatus(403);
    }

    public function test_staf_can_create_resident_and_it_is_auto_verified(): void
    {
        $payload = [
            'nik' => '3271011205920003',
            'family_card_no' => '3271011205920103',
            'name' => 'Dedi Kurniawan',
            'birth_place' => 'Bekasi',
            'birth_date' => '1992-05-12',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Belum Kawin',
            'occupation' => 'Karyawan Swasta',
            'education' => 'S1',
            'citizenship' => 'WNI',
            'address' => 'Jl. Sukamaju No. 15',
            'hamlet_id' => $this->hamlet->id,
            'rt' => '002',
            'rw' => '001',
            'family_relation' => 'Kepala Keluarga',
        ];

        $response = $this->actingAs($this->stafUser)->post('/staf/penduduk', $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $resident = Resident::where('nik', '3271011205920003')->first();
        $this->assertNotNull($resident);
        $this->assertEquals('Dedi Kurniawan', $resident->name);
        $this->assertTrue($resident->is_verified, 'Staff created resident must be auto verified');
    }

    public function test_resident_nik_must_be_exact_16_digits(): void
    {
        $invalidPayload = [
            'nik' => '12345', // Only 5 digits
            'name' => 'Nama Test',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Test',
            'rt' => '001',
            'rw' => '001',
        ];

        $response = $this->actingAs($this->stafUser)->post('/staf/penduduk', $invalidPayload);
        $response->assertSessionHasErrors('nik');
    }

    public function test_staf_can_update_resident_data_without_changing_nik(): void
    {
        $payload = [
            'name' => 'Agus Budiman Updated',
            'birth_place' => 'Tangerang',
            'birth_date' => '1980-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam Baru No. 10',
            'rt' => '002',
            'rw' => '002',
            'nik' => '9999999999999999', // attempted change
        ];

        $response = $this->actingAs($this->stafUser)->put("/staf/penduduk/{$this->wargaResident->id}", $payload);
        $response->assertSessionHasNoErrors();

        $updated = $this->wargaResident->fresh();
        $this->assertEquals('Agus Budiman Updated', $updated->name);
        $this->assertEquals('Jl. Gelam Baru No. 10', $updated->address);
        $this->assertEquals('3271010101800001', $updated->nik, 'NIK must not be modified by update');
    }

    public function test_staf_can_soft_delete_resident_without_user(): void
    {
        $unlinkedResident = Resident::create([
            'nik' => '3271019999990001',
            'name' => 'Tanpa Akun',
            'birth_place' => 'Tangerang',
            'birth_date' => '1985-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Desa',
            'rt' => '001',
            'rw' => '001',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($this->stafUser)->delete("/staf/penduduk/{$unlinkedResident->id}");
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('residents', ['id' => $unlinkedResident->id]);
    }

    public function test_staf_cannot_delete_resident_with_linked_user(): void
    {
        $response = $this->actingAs($this->stafUser)->delete("/staf/penduduk/{$this->wargaResident->id}");
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('residents', ['id' => $this->wargaResident->id, 'deleted_at' => null]);
    }

    public function test_staf_can_verify_unverified_resident(): void
    {
        $this->assertFalse($this->wargaResident->is_verified);

        $response = $this->actingAs($this->stafUser)->post("/staf/penduduk/{$this->wargaResident->id}/verify");
        $response->assertSessionHasNoErrors();

        $this->assertTrue($this->wargaResident->fresh()->is_verified);
    }

    public function test_warga_registration_creates_new_unverified_resident(): void
    {
        $response = $this->post('/auth/register', [
            'email' => 'citizen.new@gelamjaya.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => '3271018888880001',
            'name' => 'Warga Baru Fiktif',
            'birth_place' => 'Tangerang',
            'birth_date' => '1995-07-20',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Belum Kawin',
            'address' => 'Jl. Anyar No. 8',
            'rt' => '001',
            'rw' => '002',
            'hamlet_id' => $this->hamlet->id,
        ]);

        $response->assertRedirect(route('warga.profile.edit'));
        $this->assertAuthenticated();

        $resident = Resident::where('nik', '3271018888880001')->first();
        $this->assertNotNull($resident);
        $this->assertFalse($resident->is_verified, 'Registered warga must start as unverified');
    }

    public function test_warga_registration_links_existing_resident_matching_birth_date(): void
    {
        $preExistingResident = Resident::create([
            'nik' => '3271017777770001',
            'name' => 'Penduduk Terdata Staf',
            'birth_place' => 'Tangerang',
            'birth_date' => '1988-10-15',
            'gender' => 'P',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Lama No. 5',
            'rt' => '001',
            'rw' => '001',
            'is_verified' => true,
        ]);

        $response = $this->post('/register', [
            'email' => 'terdata@gelamjaya.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => '3271017777770001',
            'name' => 'Beda Nama Input', // Data in resident table must not be overwritten
            'birth_place' => 'Tangerang',
            'birth_date' => '1988-10-15', // Matches!
            'gender' => 'P',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Lama No. 5',
            'rt' => '001',
            'rw' => '001',
        ]);

        $response->assertRedirect(route('warga.profile.edit'));

        $user = User::where('email', 'terdata@gelamjaya.test')->first();
        $this->assertEquals($preExistingResident->id, $user->resident_id);
        $this->assertEquals('Penduduk Terdata Staf', $preExistingResident->fresh()->name, 'Resident data not overwritten');
    }

    public function test_warga_registration_fails_if_birth_date_does_not_match_existing_resident(): void
    {
        Resident::create([
            'nik' => '3271016666660001',
            'name' => 'Penduduk Valid',
            'birth_place' => 'Tangerang',
            'birth_date' => '1988-10-15',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Desa',
            'rt' => '001',
            'rw' => '001',
            'is_verified' => true,
        ]);

        $response = $this->post('/register', [
            'email' => 'mismatch@gelamjaya.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => '3271016666660001',
            'name' => 'Penduduk Valid',
            'birth_place' => 'Tangerang',
            'birth_date' => '1999-01-01', // Incorrect birth date
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Desa',
            'rt' => '001',
            'rw' => '001',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertGuest();
    }

    public function test_warga_registration_fails_if_nik_already_registered_to_user(): void
    {
        // $this->wargaResident already linked to $this->wargaUser
        $response = $this->post('/register', [
            'email' => 'another.warga@gelamjaya.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => $this->wargaResident->nik,
            'name' => 'Nama Sama',
            'birth_place' => 'Tangerang',
            'birth_date' => '1980-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam No. 1',
            'rt' => '001',
            'rw' => '001',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertGuest();
    }

    public function test_unverified_warga_can_update_own_profile_and_nik_cannot_change(): void
    {
        $this->assertFalse($this->wargaResident->is_verified);

        $payload = [
            'name' => 'Agus Budiman Diedit Mandiri',
            'birth_place' => 'Tangerang',
            'birth_date' => '1980-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam No. 99',
            'rt' => '003',
            'rw' => '003',
            'nik' => '8888888888888888', // tried changing NIK
        ];

        $response = $this->actingAs($this->wargaUser)->put('/warga/profil', $payload);
        $response->assertSessionHasNoErrors();

        $fresh = $this->wargaResident->fresh();
        $this->assertEquals('Agus Budiman Diedit Mandiri', $fresh->name);
        $this->assertEquals('Jl. Gelam No. 99', $fresh->address);
        $this->assertEquals('3271010101800001', $fresh->nik, 'NIK must remain original');
    }

    public function test_verified_warga_cannot_update_own_profile(): void
    {
        $this->wargaResident->update(['is_verified' => true]);

        $payload = [
            'name' => 'Mencoba Edit Saat Terverifikasi',
            'birth_place' => 'Tangerang',
            'birth_date' => '1980-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam',
            'rt' => '001',
            'rw' => '001',
        ];

        $response = $this->actingAs($this->wargaUser)->put('/warga/profil', $payload);
        $response->assertSessionHas('error');

        $this->assertNotEquals('Mencoba Edit Saat Terverifikasi', $this->wargaResident->fresh()->name);
    }
}

