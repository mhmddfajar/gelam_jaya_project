<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\LetterType;
use App\Models\LetterTypeRequirement;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected User $wargaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->adminUser = User::create([
            'name' => 'Admin Gelam',
            'email' => 'admin@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);

        // Staf User
        $this->stafUser = User::create([
            'name' => 'Staf Pelayanan',
            'email' => 'staf@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::STAF,
            'is_active' => true,
        ]);

        // Resident & Warga User
        $resident = Resident::create([
            'nik' => '3603110101900001',
            'name' => 'Warga Gelam',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Merdeka No. 10',
            'rt' => '001',
            'rw' => '001',
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
    }

    public function test_admin_can_view_letter_types_index(): void
    {
        LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'description' => 'Untuk izin usaha',
            'requirements' => ['KTP Pemohon', 'KK'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.letter-types.index'));

        $response->assertStatus(200);
    }

    public function test_staf_forbidden_from_admin_letter_types(): void
    {
        $response = $this->actingAs($this->stafUser)
            ->get(route('admin.letter-types.index'));

        $response->assertStatus(403);
    }

    public function test_warga_forbidden_from_admin_letter_types(): void
    {
        $response = $this->actingAs($this->wargaUser)
            ->get(route('admin.letter-types.index'));

        $response->assertStatus(403);
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('admin.letter-types.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_letter_type_with_requirements_and_form_fields(): void
    {
        $payload = [
            'code' => 'SKD',
            'name' => 'Surat Keterangan Domisili',
            'description' => 'Keterangan domisili tinggal warga',
            'is_active' => true,
            'requirements' => [
                'KTP Pemohon',
                'Kartu Keluarga (KK)',
                'Surat Pengantar RT/RW',
            ],
            'form_fields' => [
                [
                    'label' => 'Alamat Domisili',
                    'name' => 'alamat_domisili',
                    'type' => 'textarea',
                    'required' => true,
                    'options' => [],
                ],
                [
                    'label' => 'Keperluan Surat',
                    'name' => 'keperluan',
                    'type' => 'text',
                    'required' => true,
                    'options' => [],
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.letter-types.store'), $payload);

        $response->assertRedirect(route('admin.letter-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('letter_types', [
            'code' => 'SKD',
            'name' => 'Surat Keterangan Domisili',
            'is_active' => true,
        ]);

        $letterType = LetterType::where('code', 'SKD')->first();
        $this->assertNotNull($letterType);
        $this->assertCount(3, $letterType->requirements);
        $this->assertCount(2, $letterType->form_fields);

        // Assert relation in letter_type_requirements table
        $this->assertDatabaseCount('letter_type_requirements', 3);
        $this->assertDatabaseHas('letter_type_requirements', [
            'letter_type_id' => $letterType->id,
            'name' => 'KTP Pemohon',
        ]);
    }

    public function test_letter_type_code_must_be_unique(): void
    {
        LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.letter-types.store'), [
                'code' => 'SKU',
                'name' => 'Surat Keterangan Usaha Duplikat',
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_letter_type(): void
    {
        $letterType = LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'requirements' => ['KTP'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.letter-types.update', $letterType), [
                'code' => 'SKU',
                'name' => 'Surat Keterangan Usaha Mikro & Menengah',
                'description' => 'Update deskripsi',
                'requirements' => ['KTP Pemohon', 'Foto Usaha'],
                'form_fields' => [
                    [
                        'label' => 'Nama Usaha',
                        'name' => 'nama_usaha',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.letter-types.index'));
        $response->assertSessionHas('success');

        $letterType->refresh();
        $this->assertEquals('Surat Keterangan Usaha Mikro & Menengah', $letterType->name);
        $this->assertEquals('Update deskripsi', $letterType->description);
        $this->assertCount(2, $letterType->requirements);
        $this->assertCount(1, $letterType->form_fields);
    }

    public function test_admin_can_toggle_active_status(): void
    {
        $letterType = LetterType::create([
            'code' => 'SKTM',
            'name' => 'Surat Keterangan Tidak Mampu',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.letter-types.toggle-active', $letterType));

        $response->assertRedirect();
        $letterType->refresh();
        $this->assertFalse($letterType->is_active);

        // Toggle back to true
        $this->actingAs($this->adminUser)
            ->patch(route('admin.letter-types.toggle-active', $letterType));

        $letterType->refresh();
        $this->assertTrue($letterType->is_active);
    }

    public function test_admin_can_delete_letter_type(): void
    {
        $letterType = LetterType::create([
            'code' => 'SKP',
            'name' => 'Surat Keterangan Pindah',
            'requirements' => ['KTP Asli', 'KK Asli'],
            'is_active' => true,
        ]);

        LetterTypeRequirement::create([
            'letter_type_id' => $letterType->id,
            'name' => 'KTP Asli',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.letter-types.destroy', $letterType));

        $response->assertRedirect(route('admin.letter-types.index'));
        $this->assertDatabaseMissing('letter_types', ['id' => $letterType->id]);
        $this->assertDatabaseMissing('letter_type_requirements', ['letter_type_id' => $letterType->id]);
    }

    public function test_public_services_page_displays_active_letter_types_and_excludes_inactive(): void
    {
        $active = LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'requirements' => ['KTP Pemohon', 'KK'],
            'is_active' => true,
        ]);

        $inactive = LetterType::create([
            'code' => 'DRAFT',
            'name' => 'Surat Belum Rilis',
            'requirements' => ['KTP'],
            'is_active' => false,
        ]);

        $response = $this->get(route('public.services'));

        $response->assertStatus(200);
        $response->assertSee('SKU');
        $response->assertSee('Surat Keterangan Usaha');
        $response->assertDontSee('DRAFT');
    }
}
