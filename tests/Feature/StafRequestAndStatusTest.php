<?php

namespace Tests\Feature;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\LetterTypeRequirement;
use App\Models\RequestAttachment;
use App\Models\RequestStatusLog;
use App\Models\Resident;
use App\Models\User;
use App\Services\RequestStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StafRequestAndStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected User $wargaUser;
    protected Resident $residentWarga;
    protected Resident $residentOffline;
    protected LetterType $skuType;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

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
            'password' => bcrypt('password'),
            'role' => Role::STAF,
            'is_active' => true,
        ]);

        $this->residentWarga = Resident::create([
            'nik' => '3603110101900001',
            'name' => 'Siti Warga',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-05-15',
            'gender' => 'P',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Merdeka No. 10',
            'rt' => '001',
            'rw' => '002',
            'citizenship' => 'WNI',
            'is_verified' => false,
        ]);

        $this->wargaUser = User::create([
            'name' => 'Siti Warga',
            'email' => 'siti@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::WARGA,
            'resident_id' => $this->residentWarga->id,
            'is_active' => true,
        ]);

        $this->residentOffline = Resident::create([
            'nik' => '3603110202850002',
            'name' => 'Bambang Luring',
            'birth_place' => 'Tangerang',
            'birth_date' => '1985-08-20',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Desa Gelam No. 25',
            'rt' => '003',
            'rw' => '001',
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $this->skuType = LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'description' => 'Untuk izin usaha',
            'template' => 'Surat Keterangan Usaha untuk {{nama}}',
            'form_fields' => [
                [
                    'name' => 'nama_usaha',
                    'key' => 'nama_usaha',
                    'label' => 'Nama Usaha',
                    'type' => 'text',
                    'required' => true,
                ],
            ],
            'is_active' => true,
        ]);

        LetterTypeRequirement::create([
            'letter_type_id' => $this->skuType->id,
            'name' => 'KTP Pemohon',
            'is_required' => true,
        ]);
    }

    public function test_warga_and_guest_cannot_access_staf_request_pages(): void
    {
        $this->get('/staf/pengajuan')->assertRedirect('/login');

        $this->actingAs($this->wargaUser)
            ->get('/staf/pengajuan')
            ->assertStatus(403);

        $this->actingAs($this->wargaUser)
            ->get('/staf/pengajuan/baru')
            ->assertStatus(403);
    }

    public function test_staf_and_admin_can_access_queue_index_and_detail(): void
    {
        $request = LetterRequest::create([
            'registration_no' => 'REG-20261124-0001',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Keperluan perizinan usaha toko kelontong',
            'form_data' => ['nama_usaha' => 'Toko Berkah'],
        ]);

        $this->actingAs($this->stafUser)
            ->get('/staf/pengajuan')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Staf/Requests/Index'));

        $this->actingAs($this->adminUser)
            ->get("/staf/pengajuan/{$request->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Staf/Requests/Show'));
    }

    public function test_staf_can_submit_offline_luring_request(): void
    {
        $response = $this->actingAs($this->stafUser)->post('/staf/pengajuan', [
            'resident_id' => $this->residentOffline->id,
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Pengajuan surat luring untuk bank',
            'form_data' => [
                'nama_usaha' => 'Bengkel Mobil Maju',
            ],
        ]);

        $this->assertDatabaseHas('letter_requests', [
            'resident_id' => $this->residentOffline->id,
            'letter_type_id' => $this->skuType->id,
            'channel' => Channel::LURING->value,
            'status' => RequestStatus::DIPROSES->value,
            'submitted_by' => $this->stafUser->id,
            'purpose' => 'Pengajuan surat luring untuk bank',
        ]);

        $letterRequest = LetterRequest::where('resident_id', $this->residentOffline->id)->first();
        $this->assertNotNull($letterRequest);
        $this->assertStringStartsWith('REG-', $letterRequest->registration_no);

        // Verify initial log was created
        $this->assertDatabaseHas('request_status_logs', [
            'letter_request_id' => $letterRequest->id,
            'from_status' => null,
            'to_status' => RequestStatus::DIPROSES->value,
            'changed_by' => $this->stafUser->id,
        ]);

        $response->assertRedirect("/staf/pengajuan/{$letterRequest->id}");
    }

    public function test_offline_request_supports_optional_attachments(): void
    {
        $file = UploadedFile::fake()->create('ktp_fisik.pdf', 500, 'application/pdf');

        $this->actingAs($this->stafUser)->post('/staf/pengajuan', [
            'resident_id' => $this->residentOffline->id,
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Pengajuan dengan lampiran fisik',
            'form_data' => [
                'nama_usaha' => 'Fotokopi Bersama',
            ],
            'attachments' => [$file],
        ])->assertSessionHasNoErrors();

        $letterRequest = LetterRequest::where('purpose', 'Pengajuan dengan lampiran fisik')->first();
        $this->assertNotNull($letterRequest);
        $this->assertDatabaseHas('request_attachments', [
            'letter_request_id' => $letterRequest->id,
            'original_name' => 'ktp_fisik.pdf',
        ]);
    }

    public function test_status_transition_diajukan_to_diverifikasi(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0002',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Keperluan usaha',
        ]);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::DIVERIFIKASI->value,
                'note' => 'Berkas persyaratan telah lengkap dan cocok.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $req->refresh();
        $this->assertEquals(RequestStatus::DIVERIFIKASI, $req->status);
        $this->assertEquals('Berkas persyaratan telah lengkap dan cocok.', $req->status_note);

        $this->assertDatabaseHas('request_status_logs', [
            'letter_request_id' => $req->id,
            'from_status' => RequestStatus::DIAJUKAN->value,
            'to_status' => RequestStatus::DIVERIFIKASI->value,
            'note' => 'Berkas persyaratan telah lengkap dan cocok.',
            'changed_by' => $this->stafUser->id,
        ]);
    }

    public function test_status_transition_to_perlu_perbaikan_and_ditolak_requires_note(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0003',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Keperluan usaha',
        ]);

        // Attempt perlu_perbaikan without note -> rejected
        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::PERLU_PERBAIKAN->value,
                'note' => '',
            ])
            ->assertSessionHasErrors(['note']);

        // With note -> succeeds
        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::PERLU_PERBAIKAN->value,
                'note' => 'Foto KTP buram, mohon unggah ulang.',
            ])
            ->assertSessionHasNoErrors();

        $req->refresh();
        $this->assertEquals(RequestStatus::PERLU_PERBAIKAN, $req->status);
        $this->assertEquals('Foto KTP buram, mohon unggah ulang.', $req->status_note);

        // Attempt ditolak without note
        $req->update(['status' => RequestStatus::DIAJUKAN]);
        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::DITOLAK->value,
                'note' => '',
            ])
            ->assertSessionHasErrors(['note']);

        // Ditolak with note -> succeeds
        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::DITOLAK->value,
                'note' => 'Alamat usaha berada di luar wilayah Desa Gelam Jaya.',
            ])
            ->assertSessionHasNoErrors();

        $req->refresh();
        $this->assertEquals(RequestStatus::DITOLAK, $req->status);
    }

    public function test_terminal_status_cannot_be_changed(): void
    {
        $reqSelesai = LetterRequest::create([
            'registration_no' => 'REG-20261124-0004',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::SELESAI,
            'purpose' => 'Selesai',
            'completed_at' => now(),
        ]);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$reqSelesai->id}/status", [
                'status' => RequestStatus::DIPROSES->value,
            ])
            ->assertSessionHasErrors(['status']);

        $reqDitolak = LetterRequest::create([
            'registration_no' => 'REG-20261124-0005',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DITOLAK,
            'purpose' => 'Ditolak',
        ]);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$reqDitolak->id}/status", [
                'status' => RequestStatus::DIAJUKAN->value,
            ])
            ->assertSessionHasErrors(['status']);
    }

    public function test_siap_diambil_to_selesai_sets_completed_at(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0006',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::SIAP_DIAMBIL,
            'purpose' => 'Keperluan usaha',
        ]);

        $this->assertNull($req->completed_at);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$req->id}/status", [
                'status' => RequestStatus::SELESAI->value,
                'note' => 'Surat fisik telah diambil di kantor desa.',
            ])
            ->assertSessionHasNoErrors();

        $req->refresh();
        $this->assertEquals(RequestStatus::SELESAI, $req->status);
        $this->assertNotNull($req->completed_at);
    }

    public function test_diproses_to_selesai_restricted_for_daring_or_ungenerated_letters(): void
    {
        // 1. Channel Daring cannot jump directly from diproses to selesai
        $daringReq = LetterRequest::create([
            'registration_no' => 'REG-20261124-0007',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIPROSES,
            'purpose' => 'Daring diproses',
        ]);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$daringReq->id}/status", [
                'status' => RequestStatus::SELESAI->value,
            ])
            ->assertSessionHasErrors(['status']);

        // 2. Channel Luring without generated letter number cannot jump to selesai
        $luringReq = LetterRequest::create([
            'registration_no' => 'REG-20261124-0008',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentOffline->id,
            'submitted_by' => $this->stafUser->id,
            'channel' => Channel::LURING,
            'status' => RequestStatus::DIPROSES,
            'purpose' => 'Luring diproses',
            'letter_number' => null,
        ]);

        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$luringReq->id}/status", [
                'status' => RequestStatus::SELESAI->value,
            ])
            ->assertSessionHasErrors(['status']);

        // 3. Channel Luring WITH generated letter number CAN transition to selesai
        $luringReq->update(['letter_number' => '001/SKU/XI/2026']);
        $this->actingAs($this->stafUser)
            ->post("/staf/pengajuan/{$luringReq->id}/status", [
                'status' => RequestStatus::SELESAI->value,
                'note' => 'Surat langsung dicetak dan diserahkan kepada pemohon.',
            ])
            ->assertSessionHasNoErrors();

        $luringReq->refresh();
        $this->assertEquals(RequestStatus::SELESAI, $luringReq->status);
        $this->assertNotNull($luringReq->completed_at);
    }

    public function test_warga_resubmit_reverts_status_to_diajukan(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0009',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::PERLU_PERBAIKAN,
            'purpose' => 'Keperluan awal belum jelas',
            'status_note' => 'Mohon jelaskan nama usaha dan unggah ulang KTP.',
            'form_data' => ['nama_usaha' => 'Toko X'],
        ]);

        $newKtp = UploadedFile::fake()->create('ktp_jelas.jpg', 600, 'image/jpeg');

        $this->actingAs($this->wargaUser)
            ->post("/warga/pengajuan/{$req->id}/resubmit", [
                'purpose' => 'Keperluan pengajuan modal usaha mikro di bank',
                'form_data' => [
                    'nama_usaha' => 'Toko Kelontong Berkah Sejahtera',
                ],
                'attachments' => [
                    $this->skuType->typeRequirements->first()->id => $newKtp,
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect("/warga/pengajuan/{$req->id}");

        $req->refresh();
        $this->assertEquals(RequestStatus::DIAJUKAN, $req->status);
        $this->assertEquals('Keperluan pengajuan modal usaha mikro di bank', $req->purpose);
        $this->assertEquals('Toko Kelontong Berkah Sejahtera', $req->form_data['nama_usaha']);

        // Check status log created
        $this->assertDatabaseHas('request_status_logs', [
            'letter_request_id' => $req->id,
            'from_status' => RequestStatus::PERLU_PERBAIKAN->value,
            'to_status' => RequestStatus::DIAJUKAN->value,
            'changed_by' => $this->wargaUser->id,
        ]);
    }

    public function test_warga_cannot_resubmit_if_status_is_not_perlu_perbaikan(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0010',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Pengajuan biasa',
        ]);

        $this->actingAs($this->wargaUser)
            ->post("/warga/pengajuan/{$req->id}/resubmit", [
                'purpose' => 'Ubah data',
            ])
            ->assertStatus(403);
    }

    public function test_other_warga_cannot_resubmit_someone_elses_request(): void
    {
        $otherResident = Resident::create([
            'nik' => '3603110909990009',
            'name' => 'Warga Lain',
            'birth_place' => 'Tangerang',
            'birth_date' => '1999-09-09',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Belum Kawin',
            'address' => 'Jl. Lain',
            'rt' => '001',
            'rw' => '001',
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $otherUser = User::create([
            'name' => 'Warga Lain',
            'email' => 'lain@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::WARGA,
            'resident_id' => $otherResident->id,
            'is_active' => true,
        ]);

        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0011',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::PERLU_PERBAIKAN,
            'purpose' => 'Pengajuan Siti',
        ]);

        $this->actingAs($otherUser)
            ->post("/warga/pengajuan/{$req->id}/resubmit", [
                'purpose' => 'Mencoba hijack',
            ])
            ->assertStatus(403);
    }

    public function test_staf_can_download_private_attachment(): void
    {
        $req = LetterRequest::create([
            'registration_no' => 'REG-20261124-0012',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentWarga->id,
            'submitted_by' => $this->wargaUser->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Unduh berkas',
        ]);

        $file = UploadedFile::fake()->create('ktp_rahasia.pdf', 300, 'application/pdf');
        $storedPath = $file->store("attachments/{$req->id}", 'local');

        $attachment = RequestAttachment::create([
            'letter_request_id' => $req->id,
            'requirement_id' => $this->skuType->typeRequirements->first()->id,
            'file_path' => $storedPath,
            'original_name' => 'ktp_rahasia.pdf',
            'mime_type' => 'application/pdf',
            'size_kb' => 300,
        ]);

        $this->actingAs($this->stafUser)
            ->get("/staf/attachments/{$attachment->id}/download")
            ->assertOk()
            ->assertDownload('ktp_rahasia.pdf');
    }
}
