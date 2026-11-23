<?php

namespace Database\Seeders;

use App\Models\LetterType;
use App\Models\LetterTypeRequirement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'code' => 'SKU',
                'name' => 'Surat Keterangan Usaha',
                'description' => 'Surat keterangan resmi yang menerangkan bahwa pemohon benar memiliki dan menjalankan kegiatan usaha di wilayah Desa Gelam Jaya.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Foto Tempat Usaha',
                ],
                'form_fields' => [
                    [
                        'name' => 'nama_usaha',
                        'key' => 'nama_usaha',
                        'label' => 'Nama Usaha',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'jenis_usaha',
                        'key' => 'jenis_usaha',
                        'label' => 'Jenis / Bidang Usaha',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'alamat_usaha',
                        'key' => 'alamat_usaha',
                        'label' => 'Alamat Tempat Usaha',
                        'type' => 'textarea',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKD',
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan yang menyatakan tempat tinggal atau domisili sah seorang warga di lingkungan Desa Gelam Jaya.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'alamat_domisili',
                        'key' => 'alamat_domisili',
                        'label' => 'Alamat Domisili Saat Ini',
                        'type' => 'textarea',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'keperluan',
                        'key' => 'keperluan',
                        'label' => 'Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKTM',
                'name' => 'Surat Keterangan Tidak Mampu',
                'description' => 'Surat keterangan kondisi ekonomi keluarga kurang mampu untuk pengajuan beasiswa pendidikan, bantuan kesehatan, atau bantuan sosial.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'keperluan',
                        'key' => 'keperluan',
                        'label' => 'Keperluan Pengajuan',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            'Beasiswa Pendidikan',
                            'Bantuan Kesehatan / BPJS',
                            'Bantuan Sosial',
                            'Keringanan Biaya Rumah Sakit',
                            'Lainnya',
                        ],
                    ],
                    [
                        'name' => 'penghasilan_bulanan',
                        'key' => 'penghasilan_bulanan',
                        'label' => 'Penghasilan Per Bulan (Rp)',
                        'type' => 'number',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKCK',
                'name' => 'Surat Pengantar SKCK',
                'description' => 'Surat pengantar rekomendasi permohonan penerbitan Surat Keterangan Catatan Kepolisian di Polsek / Polres setempat.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Akta Kelahiran / Ijazah',
                ],
                'form_fields' => [
                    [
                        'name' => 'keperluan_skck',
                        'key' => 'keperluan_skck',
                        'label' => 'Keperluan Pembuatan SKCK',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKBM',
                'name' => 'Surat Keterangan Belum Menikah',
                'description' => 'Surat keterangan yang menyatakan bahwa pemohon berstatus belum pernah menikah (lajang) secara sah.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'keperluan',
                        'key' => 'keperluan',
                        'label' => 'Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKK',
                'name' => 'Surat Keterangan Kematian',
                'description' => 'Surat keterangan resmi atas peristiwa meninggalnya warga untuk pengurusan dokumen administrasi kependudukan dan waris.',
                'requirements' => [
                    'KTP Pelapor',
                    'KTP & KK Almarhum',
                    'Surat Keterangan RS/RT',
                ],
                'form_fields' => [
                    [
                        'name' => 'nama_almarhum',
                        'key' => 'nama_almarhum',
                        'label' => 'Nama Almarhum / Almarhumah',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'tanggal_meninggal',
                        'key' => 'tanggal_meninggal',
                        'label' => 'Tanggal Meninggal',
                        'type' => 'date',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'tempat_meninggal',
                        'key' => 'tempat_meninggal',
                        'label' => 'Tempat Meninggal',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'penyebab_meninggal',
                        'key' => 'penyebab_meninggal',
                        'label' => 'Penyebab Meninggal',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKL',
                'name' => 'Surat Keterangan Kelahiran',
                'description' => 'Surat keterangan kelahiran anak untuk pengurusan Akta Kelahiran resmi serta pembaruan Kartu Keluarga.',
                'requirements' => [
                    'KTP Kedua Orang Tua',
                    'Kartu Keluarga (KK)',
                    'Buku Nikah',
                    'Ket. Bidan/RS',
                ],
                'form_fields' => [
                    [
                        'name' => 'nama_bayi',
                        'key' => 'nama_bayi',
                        'label' => 'Nama Bayi',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'jenis_kelamin',
                        'key' => 'jenis_kelamin',
                        'label' => 'Jenis Kelamin',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['Laki-laki', 'Perempuan'],
                    ],
                    [
                        'name' => 'waktu_lahir',
                        'key' => 'waktu_lahir',
                        'label' => 'Tanggal & Jam Lahir',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'tempat_lahir',
                        'key' => 'tempat_lahir',
                        'label' => 'Tempat Lahir',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKP',
                'name' => 'Surat Keterangan Pindah',
                'description' => 'Surat keterangan pengantar kepindahan tempat tinggal penduduk dari wilayah Desa Gelam Jaya ke daerah tujuan baru.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK) Asli',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'alamat_tujuan',
                        'key' => 'alamat_tujuan',
                        'label' => 'Alamat Tujuan Pindah',
                        'type' => 'textarea',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'alasan_pindah',
                        'key' => 'alasan_pindah',
                        'label' => 'Alasan Pindah',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'jumlah_pengikut',
                        'key' => 'jumlah_pengikut',
                        'label' => 'Jumlah Pengikut',
                        'type' => 'number',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SPKTP',
                'name' => 'Surat Pengantar KTP / Kartu Keluarga',
                'description' => 'Surat pengantar permohonan pembuatan KTP baru, pembaharuan KK, maupun penggantian dokumen yang rusak atau hilang.',
                'requirements' => [
                    'KK Lama / Akta Lahir',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'jenis_permohonan',
                        'key' => 'jenis_permohonan',
                        'label' => 'Jenis Permohonan',
                        'type' => 'select',
                        'required' => true,
                        'options' => [
                            'Penerbitan Baru',
                            'Perubahan Data Elemen',
                            'Penggantian Hilang / Rusak',
                        ],
                    ],
                    [
                        'name' => 'keperluan',
                        'key' => 'keperluan',
                        'label' => 'Keperluan',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
            [
                'code' => 'SKH',
                'name' => 'Surat Keterangan Kehilangan',
                'description' => 'Surat pengantar keterangan kehilangan barang atau dokumen untuk dasar penerbitan laporan kepolisian setempat.',
                'requirements' => [
                    'KTP Pemohon',
                    'Kartu Keluarga (KK)',
                    'Surat Pengantar RT/RW',
                ],
                'form_fields' => [
                    [
                        'name' => 'barang_hilang',
                        'key' => 'barang_hilang',
                        'label' => 'Barang / Dokumen yang Hilang',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                    [
                        'name' => 'waktu_dan_lokasi',
                        'key' => 'waktu_dan_lokasi',
                        'label' => 'Perkiraan Tanggal & Lokasi Kejadian',
                        'type' => 'text',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($types) {
            foreach ($types as $typeData) {
                $requirements = $typeData['requirements'];

                $letterType = LetterType::updateOrCreate(
                    ['code' => $typeData['code']],
                    [
                        'name' => $typeData['name'],
                        'description' => $typeData['description'],
                        'requirements' => $requirements,
                        'form_fields' => $typeData['form_fields'],
                        'is_active' => true,
                    ]
                );

                // Sync letter_type_requirements table
                $letterType->typeRequirements()->delete();
                foreach ($requirements as $req) {
                    LetterTypeRequirement::create([
                        'letter_type_id' => $letterType->id,
                        'name' => $req,
                        'is_required' => true,
                    ]);
                }
            }
        });
    }
}
