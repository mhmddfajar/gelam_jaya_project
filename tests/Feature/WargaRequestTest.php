<?php

namespace Tests\Feature;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\LetterTypeRequirement;
use App\Models\RequestAttachment;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WargaRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $wargaUserA;
    protected Resident $residentA;
    protected User $wargaUserB;
    protected Resident $residentB;
    protected LetterType $skuType;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        // Resident A & Warga User A
        $this->residentA = Resident::create([
            'nik' => '3603110101900001',
            'name' => 'Ahmad Warga',
            'birth_place' => 'Tangerang',
            'birth_date' => '1990-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Kawin',
            'address' => 'Jl. Gelam Raya No. 1',
            'rt' => '001',
            'rw' => '001',
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $this->wargaUserA = User::create([
            'name' => 'Ahmad Warga',
            'email' => 'ahmad@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::WARGA,
            'resident_id' => $this->residentA->id,
            'is_active' => true,
        ]);

        // Resident B & Warga User B
        $this->residentB = Resident::create([
            'nik' => '3603110202950002',
            'name' => 'Budi Warga',
            'birth_place' => 'Tangerang',
            'birth_date' => '1995-02-02',
            'gender' => 'L',
            'religion' => 'Islam',
            'marital_status' => 'Belum Kawin',
            'address' => 'Jl. Gelam Raya No. 2',
            'rt' => '002',
            'rw' => '001',
            'citizenship' => 'WNI',
            'is_verified' => true,
        ]);

        $this->wargaUserB = User::create([
            'name' => 'Budi Warga',
            'email' => 'budi@gelamjaya.test',
            'password' => bcrypt('password'),
            'role' => Role::WARGA,
            'resident_id' => $this->residentB->id,
            'is_active' => true,
        ]);

        // Setup Letter Type (SKU)
        $this->skuType = LetterType::create([
            'code' => 'SKU',
            'name' => 'Surat Keterangan Usaha',
            'description' => 'Untuk pengajuan izin dan modal usaha',
            'requirements' => ['KTP Pemohon', 'Kartu Keluarga (KK)'],
            'form_fields' => [
                [
                    'name' => 'nama_usaha',
                    'key' => 'nama_usaha',
                    'label' => 'Nama Usaha',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'name' => 'jenis_usaha',
                    'key' => 'jenis_usaha',
                    'label' => 'Jenis Usaha',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Perdagangan', 'Jasa', 'Kuliner'],
                ],
                [
                    'name' => 'omset_bulanan',
                    'key' => 'omset_bulanan',
                    'label' => 'Estimasi Omset',
                    'type' => 'number',
                    'required' => false,
                ],
            ],
            'is_active' => true,
        ]);

        LetterTypeRequirement::create([
            'letter_type_id' => $this->skuType->id,
            'name' => 'KTP Pemohon',
            'is_required' => true,
        ]);

        LetterTypeRequirement::create([
            'letter_type_id' => $this->skuType->id,
            'name' => 'Kartu Keluarga (KK)',
            'is_required' => true,
        ]);
    }

    public function test_warga_can_view_requests_index(): void
    {
        $response = $this->actingAs($this->wargaUserA)
            ->get(route('warga.requests.index'));

        $response->assertStatus(200);
    }

    public function test_guest_redirected_to_login_from_warga_requests(): void
    {
        $response = $this->get(route('warga.requests.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_warga_can_view_create_page(): void
    {
        $response = $this->actingAs($this->wargaUserA)
            ->get(route('warga.requests.create'));

        $response->assertStatus(200);
    }

    public function test_warga_can_submit_daring_request_with_attachments(): void
    {
        $fileKtp = UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf');
        $fileKk = UploadedFile::fake()->create('kk.jpg', 300, 'image/jpeg');

        $payload = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Persyaratan pengajuan modal usaha KUR BRI',
            'form_data' => [
                'nama_usaha' => 'Warung Berkah Gelam',
                'jenis_usaha' => 'Perdagangan',
                'omset_bulanan' => 5000000,
            ],
            'attachments' => [
                $fileKtp,
                $fileKk,
            ],
        ];

        $response = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload);

        $this->assertDatabaseHas('letter_requests', [
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentA->id,
            'submitted_by' => $this->wargaUserA->id,
            'channel' => Channel::DARING->value,
            'status' => RequestStatus::DIAJUKAN->value,
            'purpose' => 'Persyaratan pengajuan modal usaha KUR BRI',
        ]);

        $createdRequest = LetterRequest::where('resident_id', $this->residentA->id)->first();
        $this->assertNotNull($createdRequest);
        $this->assertStringStartsWith('REG-', $createdRequest->registration_no);

        $response->assertRedirect(route('warga.requests.show', $createdRequest->id));
        $response->assertSessionHas('success');

        // Check attachments and storage
        $this->assertDatabaseCount('request_attachments', 2);
        $attachment = RequestAttachment::where('letter_request_id', $createdRequest->id)->first();
        Storage::disk('local')->assertExists($attachment->file_path);

        // Check initial status log
        $this->assertDatabaseHas('request_status_logs', [
            'letter_request_id' => $createdRequest->id,
            'from_status' => null,
            'to_status' => RequestStatus::DIAJUKAN->value,
            'changed_by' => $this->wargaUserA->id,
        ]);
    }

    public function test_warga_cannot_submit_duplicate_active_request_for_same_letter_type(): void
    {
        // Active request already exists
        LetterRequest::create([
            'registration_no' => 'REG-20261124-0001',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentA->id,
            'submitted_by' => $this->wargaUserA->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Pengajuan aktif sebelumnya',
        ]);

        $fileKtp = UploadedFile::fake()->create('ktp.pdf', 300);

        $payload = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Mencoba mengajukan lagi jenis surat yang sama',
            'form_data' => [
                'nama_usaha' => 'Usaha Baru',
                'jenis_usaha' => 'Jasa',
            ],
            'attachments' => [$fileKtp],
        ];

        $response = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload);

        $response->assertSessionHasErrors('letter_type_id');
    }

    public function test_warga_can_submit_again_if_previous_request_is_completed_or_rejected(): void
    {
        // Previous request is selesai
        LetterRequest::create([
            'registration_no' => 'REG-20261120-0001',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentA->id,
            'submitted_by' => $this->wargaUserA->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::SELESAI,
            'purpose' => 'Pengajuan lama yang sudah selesai',
        ]);

        $fileKtp = UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf');
        $fileKk = UploadedFile::fake()->create('kk.jpg', 300, 'image/jpeg');

        $payload = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Pengajuan periode baru',
            'form_data' => [
                'nama_usaha' => 'Usaha Baru',
                'jenis_usaha' => 'Jasa',
            ],
            'attachments' => [$fileKtp, $fileKk],
        ];

        $response = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_dynamic_form_schema_validation_rejects_missing_required_fields(): void
    {
        $fileKtp = UploadedFile::fake()->create('ktp.pdf', 300);

        // Missing 'nama_usaha' which is required
        $payload = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Keperluan pengajuan',
            'form_data' => [
                'jenis_usaha' => 'Perdagangan',
            ],
            'attachments' => [$fileKtp],
        ];

        $response = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload);

        $response->assertSessionHasErrors('form_data.nama_usaha');
    }

    public function test_attachment_validation_rejects_disallowed_formats_and_oversized_files(): void
    {
        // Disallowed format .exe
        $invalidFile = UploadedFile::fake()->create('malicious.exe', 500);

        $payload = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Keperluan pengajuan',
            'attachments' => [$invalidFile],
        ];

        $response = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload);

        $response->assertSessionHasErrors('attachments.0');

        // Oversized file > 2MB
        $oversizedFile = UploadedFile::fake()->create('huge.pdf', 3000);
        $payload2 = [
            'letter_type_id' => $this->skuType->id,
            'purpose' => 'Keperluan pengajuan',
            'attachments' => [$oversizedFile],
        ];

        $response2 = $this->actingAs($this->wargaUserA)
            ->post(route('warga.requests.store'), $payload2);

        $response2->assertSessionHasErrors('attachments.0');
    }

    public function test_warga_can_view_own_request_and_download_own_attachment(): void
    {
        $filePath = 'attachments/1/ktp_ahmad.pdf';
        Storage::disk('local')->put($filePath, 'fake content');

        $request = LetterRequest::create([
            'registration_no' => 'REG-20261124-0001',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentA->id,
            'submitted_by' => $this->wargaUserA->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Pengajuan sendiri',
        ]);

        $attachment = RequestAttachment::create([
            'letter_request_id' => $request->id,
            'file_path' => $filePath,
            'original_name' => 'ktp_ahmad.pdf',
            'mime_type' => 'application/pdf',
            'size_kb' => 12,
        ]);

        // Can view detail
        $viewResponse = $this->actingAs($this->wargaUserA)
            ->get(route('warga.requests.show', $request));
        $viewResponse->assertStatus(200);

        // Can download attachment
        $downloadResponse = $this->actingAs($this->wargaUserA)
            ->get(route('warga.requests.attachments.download', [$request, $attachment]));
        $downloadResponse->assertStatus(200);
    }

    public function test_warga_forbidden_from_viewing_or_downloading_other_warga_request_and_attachment(): void
    {
        $filePath = 'attachments/1/ktp_ahmad.pdf';
        Storage::disk('local')->put($filePath, 'fake content');

        // Request belonging to Warga A
        $requestA = LetterRequest::create([
            'registration_no' => 'REG-20261124-0001',
            'letter_type_id' => $this->skuType->id,
            'resident_id' => $this->residentA->id,
            'submitted_by' => $this->wargaUserA->id,
            'channel' => Channel::DARING,
            'status' => RequestStatus::DIAJUKAN,
            'purpose' => 'Pengajuan rahasia warga A',
        ]);

        $attachmentA = RequestAttachment::create([
            'letter_request_id' => $requestA->id,
            'file_path' => $filePath,
            'original_name' => 'ktp_ahmad.pdf',
            'mime_type' => 'application/pdf',
            'size_kb' => 12,
        ]);

        // Warga B tries to view Warga A's request -> 403
        $viewResponse = $this->actingAs($this->wargaUserB)
            ->get(route('warga.requests.show', $requestA));
        $viewResponse->assertStatus(403);

        // Warga B tries to download Warga A's attachment -> 403
        $downloadResponse = $this->actingAs($this->wargaUserB)
            ->get(route('warga.requests.attachments.download', [$requestA, $attachmentA]));
        $downloadResponse->assertStatus(403);
    }
}
