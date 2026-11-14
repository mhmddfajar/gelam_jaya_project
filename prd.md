# PRD Teknis (untuk AI Coding Agent) - Stack Inertia.js

**Proyek:** Sistem Informasi Pelayanan Administrasi Desa Berbasis Web dengan Pengajuan Daring dan Luring Terintegrasi (Kantor Desa Gelam Jaya)  
**Versi dokumen:** 2.0 (perubahan: Stack diperbarui menjadi Monolith Laravel + Vue 3 via Inertia.js; menghapus REST API JSON terpisah, Sanctum token, Axios, dan Vue Router; modul Surat Menyurat dan Laporan tetap dipindah ke fase paling akhir)  
**Fungsi dokumen:** sumber kebenaran tunggal untuk implementasi. Agent tidak boleh mengarang fitur, tabel, kolom, route, atau library di luar dokumen ini.

---

## 0. Aturan Wajib untuk Agent

1. Baca seluruh dokumen sebelum menulis kode.
2. Kerjakan **satu fase pada satu waktu** (lihat Bagian 14). Setelah fase selesai: jalankan test, tulis ringkasan perubahan, lalu **BERHENTI** dan tunggu perintah fase berikutnya.
3. Jangan menambah fitur, library, tabel, kolom, atau route yang tidak ada di dokumen. Jika menurut agent perlu, **usulkan dulu**, jangan langsung dikerjakan.
4. Jangan mengubah migration yang sudah dibuat. Perubahan skema = migration baru.
5. Jika ada hal ambigu, cek Bagian 15 (Asumsi). Jika tidak tercakup, **tanya**, jangan menebak.
6. Bahasa: nama variabel, kelas, tabel, kolom, route, dan pesan commit memakai **Inggris**. Teks yang tampil ke pengguna (label, tombol, pesan error, isi surat) memakai **Bahasa Indonesia**. Komentar kode memakai Inggris.
7. Data contoh (seeder) harus **fiktif**. Dilarang memakai NIK atau nama warga asli.
8. Jangan refactor, rename, atau menghapus file di luar cakupan fase yang sedang dikerjakan.
9. Setiap fase wajib punya test (Bagian 14). Fase dianggap selesai hanya jika semua test lulus.
10. Jangan commit secret. Sediakan `.env.example`.
11. Modul Surat Menyurat dan Laporan (Fase 4 sampai 8) **dikerjakan paling akhir** dan hanya setelah pemilik proyek menyerahkan data wawancara (lihat Gerbang Data Wawancara di Bagian 14). Jangan mengarang jenis surat, persyaratan, atau isian surat.

---

## 1. Ringkasan Produk

Sistem web untuk Kantor Desa Gelam Jaya dengan **dua jalur layanan surat dalam satu alur**:

- **Daring:** warga login, mengajukan surat, mengunggah berkas persyaratan, memantau status lewat badge.
- **Luring:** warga datang ke kantor desa, **staf yang menginput** pengajuan pada sistem yang sama.

Kedua jalur tersimpan di tabel yang sama (dibedakan kolom `channel`), sehingga **laporan layanan harian dibuat otomatis**.

Tanda tangan dan stempel surat **tetap manual** di kantor desa. Sistem hanya membuat draf surat PDF dari template.

Sistem memakai **3 peran**: `warga`, `staf`, dan `admin`. Akun `admin` dipegang kepala desa/pimpinan. Tidak ada peran `kepala_desa` terpisah.

Modul:

| Prioritas | Modul |
|---|---|
| P1 (inti) | Autentikasi dan peran, Data penduduk, Surat menyurat, Laporan dan dashboard |
| P2 (pendukung) | Identitas dan wilayah, Visi misi dan pemerintahan, Potensi desa, Sarana dan prasarana |

**Catatan urutan pengerjaan:** prioritas (P1/P2) tidak sama dengan urutan pengerjaan. Karena data wawancara untuk jenis surat belum ada, modul Surat Menyurat dan Laporan dikerjakan **paling akhir** (Fase 4-8), setelah Data Penduduk dan modul Info Desa selesai.

---

## 2. Tech Stack

| Lapisan | Teknologi | Keterangan |
|---|---|---|
| Arsitektur | Monolith Integrated | Laravel + Inertia.js (Single Repository) |
| Backend | Laravel (PHP 8.2+) | Standard Controllers, Web Middleware Session Auth |
| Frontend | Vue 3 (Composition API, `<script setup>`) | Terhubung via Inertia.js Adapter (`@inertiajs/vue-3`) |
| Bundler | Vite | Vite plugin bawaan Laravel |
| Autentikasi | Laravel Web Session Auth | Guard `web` bawaan Laravel (Session Cookie, CSRF) |
| Database | MySQL 8 | Eloquent ORM, Migrations, Seeders |
| Styling | Tailwind CSS v4 | Tanpa library UI tambahan (seperti PrimeVue/Vuetify) |
| PDF | `barryvdh/laravel-dompdf` | Generasi draf surat dan laporan |
| Excel | `maatwebsite/excel` | Ekspor laporan layanan |
| Test | Pest atau PHPUnit | Feature test untuk Controller, Service, dan Transisi Status |

Library lain **tidak boleh** ditambahkan tanpa persetujuan.

---

## 3. Struktur Repositori

```text
/
├─ app/
│  ├─ Enums/                     # PHP backed enums (Role, RequestStatus, Channel, ...)
│  ├─ Http/
│  │  ├─ Controllers/            # Inertia Web Controllers
│  │  │  ├─ Auth/                # LoginController, RegisterController, PasswordController
│  │  │  ├─ PublicController.php
│  │  │  ├─ Warga/               # RequestController, ProfileController
│  │  │  ├─ Staf/                # RequestController, ResidentController, ReportController
│  │  │  └─ Admin/               # DashboardController, UserController, LetterTypeController, VillageController
│  │  ├─ Middleware/            # HandleInertiaRequests, RoleMiddleware
│  │  └─ Requests/               # FormRequest untuk semua validasi
│  ├─ Models/
│  ├─ Policies/
│  └─ Services/                  # Logika bisnis (Status, Letter Render, Reports)
├─ database/{migrations,seeders,factories}/
├─ resources/
│  ├─ js/                        # Frontend Vue 3 (Inertia Pages & Components)
│  │  ├─ app.js                  # Entry point Inertia initialization
│  │  ├─ Components/             # StatusBadge, DataTable, FormField, FileUpload, AppToast, ConfirmDialog
│  │  ├─ Layouts/                # AppLayout, GuestLayout, WargaLayout, StafLayout, AdminLayout
│  │  └─ Pages/                  # Inertia Page Components
│  │     ├─ Auth/                # Login.vue, Register.vue, ChangePassword.vue
│  │     ├─ Public/              # Home.vue, Services.vue, Profile.vue, Potentials.vue, Facilities.vue
│  │     ├─ Warga/               # Requests/Index.vue, Create.vue, Show.vue, Profile.vue
│  │     ├─ Staf/                # Requests/Index.vue, Create.vue, Show.vue, Residents/Index.vue, Reports/Index.vue
│  │     └─ Admin/               # Dashboard.vue, Users/Index.vue, LetterTypes/Index.vue, Village/Index.vue
│  └─ views/
│     ├─ app.blade.php           # Master Blade HTML untuk Inertia
│     └─ pdf/                    # Template Blade untuk ekspor PDF (Surat & Laporan)
├─ routes/
│  ├─ web.php                    # Semua Route web aplikasi
│  └─ auth.php                   # Route autentikasi
├─ tests/Feature/
├─ vite.config.js
└─ PRD.md                        # dokumen ini
```

---

## 4. Peran dan Hak Akses

Enum `Role`: `warga`, `staf`, `admin`.

`admin` adalah akun yang dipegang kepala desa/pimpinan. Admin bisa melakukan semua yang bisa dilakukan staf, ditambah dashboard pemantauan dan pengaturan sistem. **Tidak ada role `kepala_desa`.**

| Kemampuan | warga | staf | admin |
|---|---|---|---|
| Register dan login | ✔ | ✔ | ✔ |
| Lihat halaman publik | ✔ | ✔ | ✔ |
| Ajukan surat daring (untuk dirinya sendiri) | ✔ | | |
| Lihat pengajuan milik sendiri | ✔ | | |
| Perbaiki dan kirim ulang pengajuan (status `perlu_perbaikan`) | ✔ | | |
| Ubah Data Diri sendiri (selama belum terverifikasi) | ✔ | | |
| Ganti password sendiri | ✔ | ✔ | ✔ |
| Lihat semua pengajuan | | ✔ | ✔ |
| Input pengajuan luring | | ✔ | ✔ |
| Ubah status pengajuan | | ✔ | ✔ |
| Generate dan unduh surat PDF | | ✔ | ✔ |
| CRUD data penduduk dan statistik penduduk | | ✔ | ✔ |
| Verifikasi data diri warga | | ✔ | ✔ |
| Laporan harian/periode dan ekspor | | ✔ | ✔ |
| Dashboard pemantauan | | | ✔ |
| Kelola Potensi Desa dan Sarana & Prasarana | | ✔ | ✔ |
| Kelola Identitas & Wilayah (profil desa, batas, dusun) | | | ✔ |
| Kelola Visi Misi & Pemerintahan (visi misi, perangkat desa) | | | ✔ |
| CRUD jenis surat, persyaratan, dan template | | | ✔ |
| CRUD pengguna | | | ✔ |
| Reset password pengguna | | | ✔ |

Aturan keras:
- Warga **hanya** boleh mengakses data pengajuan dan lampiran miliknya (cek via Policy di Controller).
- Hanya `staf` dan `admin` yang boleh mengubah status pengajuan.
- Fitur khusus admin (pengguna, jenis surat dan template, identitas dan wilayah, visi misi dan pemerintahan, dashboard) harus mengembalikan **403** (atau abort 403) untuk staf dan warga.
- Admin tidak bisa menonaktifkan akunnya sendiri.

---

## 5. Skema Database

Semua tabel memakai `id` bigint unsigned auto-increment dan `timestamps` kecuali disebut lain. Gunakan foreign key dengan constraint.

### 5.1 `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| name | string(150) | |
| email | string(150) unique | dipakai untuk login semua peran |
| password | string | hash |
| role | enum(`warga`,`staf`,`admin`) | index |
| resident_id | FK residents nullable unique | wajib terisi untuk role warga (diisi saat registrasi), null untuk staf/admin; satu penduduk hanya boleh tertaut ke satu akun |
| is_active | boolean default true | |

### 5.2 `hamlets` (dusun)
| Kolom | Tipe |
|---|---|
| name | string(100) unique |
| head_name | string(150) nullable |

### 5.3 `residents` (soft deletes)
| Kolom | Tipe | Keterangan |
|---|---|---|
| nik | char(16) unique | numerik 16 digit |
| family_card_no | char(16) index | no KK |
| name | string(150) | |
| birth_place | string(100) | |
| birth_date | date | |
| gender | enum(`L`,`P`) | |
| religion | string(30) | |
| marital_status | string(30) | |
| occupation | string(100) nullable | |
| education | string(50) nullable | |
| citizenship | string(10) default `WNI` | |
| address | string(255) | |
| hamlet_id | FK hamlets nullable | |
| rt | string(3) | |
| rw | string(3) | |
| family_relation | string(50) nullable | hubungan dalam keluarga |
| is_verified | boolean default false | di-set `true` oleh service jika dibuat atau diverifikasi staf/admin; `false` jika dibuat warga lewat registrasi |

### 5.4 `letter_types`
| Kolom | Tipe | Keterangan |
|---|---|---|
| code | string(20) unique | contoh `SKD` |
| name | string(150) | contoh `Surat Keterangan Domisili` |
| description | text nullable | |
| template | longText | isi surat dengan placeholder (Bagian 9) |
| form_schema | json nullable | definisi isian tambahan per jenis surat |
| is_active | boolean default true | |

**Format `form_schema`** (JSON array; `null` atau `[]` berarti tidak ada isian tambahan):
```json
[
  { "key": "nama_usaha", "label": "Nama Usaha", "type": "text", "required": true },
  { "key": "jenis_usaha", "label": "Jenis Usaha", "type": "select", "required": true, "options": ["Perdagangan", "Jasa", "Pertanian"] }
]
```

Aturan:
- `key`: huruf kecil, angka, underscore; unik dalam satu jenis surat.
- `type`: `text`, `textarea`, `number`, `date`, atau `select` (`options` wajib untuk `select`).
- `required`: boolean. Maksimal 15 field per jenis surat.
- Nilai isian disimpan di `letter_requests.form_data` sebagai objek `{ key: value }`.
- Form pengajuan dirender **dinamis** dari schema ini di Vue component.

### 5.5 `letter_type_requirements`
| Kolom | Tipe |
|---|---|
| letter_type_id | FK letter_types cascade |
| name | string(150) |
| is_required | boolean default true |

### 5.6 `officials` (perangkat desa)
| Kolom | Tipe | Keterangan |
|---|---|---|
| name | string(150) | |
| position | string(100) | contoh `Kepala Desa` |
| photo_path | string nullable | |
| period_start | smallint nullable | tahun |
| period_end | smallint nullable | tahun |
| can_sign | boolean default false | boleh jadi penandatangan surat |
| sort_order | smallint default 0 | |
| is_active | boolean default true | |

### 5.7 `letter_requests`
| Kolom | Tipe | Keterangan |
|---|---|---|
| registration_no | string(30) unique | format Bagian 7 |
| letter_type_id | FK letter_types | |
| resident_id | FK residents | subjek surat |
| submitted_by | FK users | pembuat pengajuan |
| channel | enum(`daring`,`luring`) | index |
| status | enum (Bagian 6) | index |
| purpose | string(255) | keperluan |
| form_data | json nullable | isian tambahan per jenis surat |
| status_note | text nullable | catatan staf terakhir (perbaikan/penolakan) |
| letter_number | string(50) nullable unique | |
| letter_date | date nullable | |
| signer_id | FK officials nullable | |
| completed_at | timestamp nullable | |

Index tambahan: `(status)`, `(channel)`, `(created_at)`. Pengajuan **tidak boleh dihapus permanen**.

### 5.8 `request_attachments`
| Kolom | Tipe |
|---|---|
| letter_request_id | FK letter_requests cascade |
| requirement_id | FK letter_type_requirements nullable |
| file_path | string |
| original_name | string |
| mime_type | string(100) |
| size_kb | unsigned int |

### 5.9 `request_status_logs` (hanya `created_at`)
| Kolom | Tipe |
|---|---|
| letter_request_id | FK letter_requests cascade |
| from_status | string nullable |
| to_status | string |
| note | text nullable |
| changed_by | FK users |

### 5.10 `village_profiles` (hanya **satu baris**)
| Kolom | Tipe |
|---|---|
| name | string(150) |
| village_code | string(30) nullable |
| address | string(255) nullable |
| phone | string(30) nullable |
| email | string(150) nullable |
| district | string(100) (kecamatan) |
| regency | string(100) (kabupaten) |
| province | string(100) |
| postal_code | string(10) nullable |
| logo_path | string nullable |
| area_size_ha | decimal(10,2) nullable |
| boundary_north / boundary_south / boundary_east / boundary_west | string(150) nullable |
| vision | text nullable |
| mission | longText nullable |

### 5.11 `village_potentials`
| Kolom | Tipe | Keterangan |
|---|---|---|
| title | string(150) | |
| category | enum(`pertanian`,`peternakan`,`perikanan`,`umkm`,`wisata`,`lainnya`) | |
| description | text | |
| location | string(150) nullable | |
| photo_path | string nullable | |

### 5.12 `facilities`
| Kolom | Tipe | Keterangan |
|---|---|---|
| name | string(150) | |
| category | enum(`pendidikan`,`kesehatan`,`ibadah`,`olahraga`,`jalan_transportasi`,`fasilitas_umum`) | |
| location | string(150) nullable | |
| quantity | unsigned smallint default 1 | |
| condition | enum(`baik`,`rusak_ringan`,`rusak_berat`) | |
| description | text nullable | |

---

## 6. Status Pengajuan dan Transisi

Enum `RequestStatus` (value → label UI):

| Value | Label | Warna badge |
|---|---|---|
| `diajukan` | Diajukan | biru |
| `perlu_perbaikan` | Perlu Perbaikan | oranye |
| `diverifikasi` | Diverifikasi | indigo |
| `diproses` | Diproses | kuning |
| `siap_diambil` | Siap Diambil | teal |
| `selesai` | Selesai | hijau |
| `ditolak` | Ditolak | merah |

Transisi yang **diizinkan** (selain ini harus ditolak dengan validasi error):

| Dari | Ke | Oleh | Catatan |
|---|---|---|---|
| (baru, daring) | `diajukan` | warga | status awal pengajuan daring |
| (baru, luring) | `diproses` | staf/admin | status awal pengajuan luring |
| `diajukan` | `diverifikasi` | staf/admin | |
| `diajukan` | `perlu_perbaikan` | staf/admin | `note` wajib |
| `diajukan` | `ditolak` | staf/admin | `note` wajib |
| `perlu_perbaikan` | `diajukan` | warga | lewat aksi resubmit |
| `diverifikasi` | `diproses` | staf/admin | |
| `diverifikasi` | `ditolak` | staf/admin | `note` wajib |
| `diproses` | `siap_diambil` | staf/admin | **hanya lewat aksi generate-letter** |
| `siap_diambil` | `selesai` | staf/admin | isi `completed_at` |
| `diproses` | `selesai` | staf/admin | **hanya jika `channel = luring`** dan surat sudah digenerate |

`selesai` dan `ditolak` adalah status akhir (tidak bisa diubah lagi). Setiap transisi **wajib** menulis satu baris `request_status_logs` dalam satu DB transaction via `Services/RequestStatusService`.

---

## 7. Aturan Bisnis

**Akun dan Data Diri Warga**
- Registrasi warga **langsung membuat akun aktif dan menyimpan data penduduknya ke database** dalam satu transaction.
- Field registrasi: `email`, `password`, `password_confirmation`, dan Data Diri sesuai KTP:
  - wajib: `nik` (16 digit), `name`, `birth_place`, `birth_date`, `gender`, `religion`, `marital_status`, `address`, `rt`, `rw`
  - opsional: `family_card_no`, `occupation`, `education`, `citizenship` (default `WNI`), `hamlet_id`, `family_relation`
- Hasil penyimpanan berdasarkan NIK:
  1. NIK **belum ada** di `residents`: buat baris baru dengan `is_verified = false`, lalu buat akun `warga` yang tertaut.
  2. NIK **sudah ada** (diinput staf lebih dulu) dan belum tertaut ke akun lain: tautkan akun ke baris itu **hanya jika `birth_date` cocok**. Data tidak ditimpa. Jika tidak cocok: kirim validation error (`Data tidak cocok dengan data penduduk. Hubungi kantor desa.`).
  3. NIK sudah tertaut ke akun lain: kirim validation error (`NIK sudah terdaftar pada akun lain. Hubungi kantor desa.`).
- Setelah registrasi, warga otomatis authenticated session dan diarahkan ke dashboard warga.
- Semua peran login dengan **email + password** (Web Session). Tidak ada verifikasi email & lupa password email; **admin yang mereset password** pengguna.
- Selama `is_verified = false`, warga boleh mengubah Data Diri **kecuali NIK** (`PUT /warga/profil`). Setelah `true`, hanya staf/admin yang boleh mengubahnya.
- Staf/admin memverifikasi data warga (`POST /staf/penduduk/{id}/verify`). Detail pengajuan menampilkan penanda **Data diri belum terverifikasi** bila `is_verified = false`.

**Pengajuan Daring (warga)**
- `resident_id` otomatis = `resident_id` milik user yang login.
- `form_data` divalidasi terhadap `form_schema` jenis surat.
- Semua persyaratan dengan `is_required = true` wajib punya satu berkas.
- Berkas: `jpg`, `jpeg`, `png`, `pdf`; maksimal 2 MB per berkas; maksimal 5 berkas per pengajuan.
- Berkas disimpan di disk **privat** (`storage/app/private/attachments/{request_id}/`) dan diunduh via Controller yang mengecek Policy.
- Warga tidak boleh punya dua pengajuan **aktif** (status bukan `selesai`/`ditolak`) untuk jenis surat yang sama.

**Pengajuan Luring (staf)**
- Staf memilih penduduk, jenis surat, keperluan, dan mengisi `form_data`. Berkas opsional.
- Jika warga belum terdata, form menyediakan tombol/modal **Tambah penduduk baru** (memakai `POST /staf/penduduk`, otomatis `is_verified = true`).
- `channel = luring`, `submitted_by` = staf, status awal `diproses`.

**Nomor registrasi:** `REG-YYYYMMDD-NNNN` (NNNN = urutan per hari, 4 digit, mulai 0001).
**Nomor surat:** `NNN/KODE/BULAN_ROMAWI/TAHUN` (NNN = urutan per tahun, 3 digit). Dibuat saat generate-letter.
**Penandatangan:** harus dipilih dari `officials` dengan `can_sign = true` dan `is_active = true`.
**Zona waktu dan locale:** `Asia/Jakarta`, locale `id`. Tanggal di UI: `dd MMMM yyyy`.
**Pagination:** default 15 per halaman (dikirim via Inertia paginated props).

---

## 8. Routing dan Integrasi Controller (Web Routes)

Semua route didefinisikan di `routes/web.php`. Data di-pass dari Controller ke Vue via `Inertia::render('Path/To/Page', $props)`.

### 8.1 Public Routes (Tanpa Login)
| Method | Path | Controller / Action | View Component |
|---|---|---|---|
| GET | `/` | `PublicController@home` | `Pages/Public/Home.vue` |
| GET | `/layanan` | `PublicController@services` | `Pages/Public/Services.vue` |
| GET | `/profil` | `PublicController@profile` | `Pages/Public/Profile.vue` |
| GET | `/potensi` | `PublicController@potentials` | `Pages/Public/Potentials.vue` |
| GET | `/sarana` | `PublicController@facilities` | `Pages/Public/Facilities.vue` |

### 8.2 Auth Routes (Guest / Authenticated)
| Method | Path | Akses | Action / Description |
|---|---|---|---|
| GET / POST | `/login` | Guest | `Auth\LoginController` (show / store session) |
| GET / POST | `/register` | Guest | `Auth\RegisterController` (show / store registrasi warga) |
| POST | `/logout` | Auth | `Auth\LoginController@destroy` (destroy session) |
| GET / PUT | `/akun/ganti-password` | Auth | `Auth\PasswordController` (show / update) |

### 8.3 Warga Routes (Middleware: `auth`, `role:warga`)
| Method | Path | Action / Description | View Component |
|---|---|---|---|
| GET | `/warga/pengajuan` | Index pengajuan warga | `Pages/Warga/Requests/Index.vue` |
| GET | `/warga/pengajuan/baru` | Form pengajuan baru | `Pages/Warga/Requests/Create.vue` |
| POST | `/warga/pengajuan` | Simpan pengajuan | Redirect to Index / Show |
| GET | `/warga/pengajuan/{id}` | Detail pengajuan | `Pages/Warga/Requests/Show.vue` |
| POST | `/warga/pengajuan/{id}/resubmit` | Resubmit pengajuan `perlu_perbaikan` | Redirect to Show |
| GET / PUT | `/warga/profil` | Edit Data Diri (jika belum verifikasi) | `Pages/Warga/Profile.vue` |

### 8.4 Staf & Admin Routes (Middleware: `auth`, `role:staf,admin`)
| Method | Path | Action / Description | View Component |
|---|---|---|---|
| GET | `/staf/pengajuan` | Antrean semua pengajuan | `Pages/Staf/Requests/Index.vue` |
| GET | `/staf/pengajuan/baru` | Form input luring | `Pages/Staf/Requests/Create.vue` |
| POST | `/staf/pengajuan` | Simpan pengajuan luring | Redirect to Show |
| GET | `/staf/pengajuan/{id}` | Detail & kontrol status | `Pages/Staf/Requests/Show.vue` |
| POST | `/staf/pengajuan/{id}/status` | Ubah status pengajuan | Redirect Back |
| POST | `/staf/pengajuan/{id}/generate` | Generate nomor & surat PDF | Redirect Back |
| GET | `/staf/pengajuan/{id}/letter` | Download / Stream PDF surat | File Response (PDF) |
| GET | `/staf/attachments/{id}/download` | Download lampiran privat | File Response |
| GET / POST | `/staf/penduduk` | List & Tambah Penduduk | `Pages/Staf/Residents/Index.vue` |
| PUT | `/staf/penduduk/{id}` | Update Data Penduduk | Redirect Back |
| DELETE | `/staf/penduduk/{id}` | Soft Delete Penduduk | Redirect Back |
| POST | `/staf/penduduk/{id}/verify` | Set `is_verified = true` | Redirect Back |
| GET | `/staf/laporan` | View laporan harian & periode | `Pages/Staf/Reports/Index.vue` |
| GET | `/staf/laporan/export` | Export PDF / Excel Laporan | File Download Response |
| CRUD | `/staf/desa/potensi` | CRUD Potensi Desa | `Pages/Staf/Village/Potentials.vue` |
| CRUD | `/staf/desa/sarana` | CRUD Sarana & Prasarana | `Pages/Staf/Village/Facilities.vue` |

### 8.5 Admin Only Routes (Middleware: `auth`, `role:admin`)
| Method | Path | Action / Description | View Component |
|---|---|---|---|
| GET | `/admin/dashboard` | Dashboard Pemantauan | `Pages/Admin/Dashboard.vue` |
| CRUD | `/admin/pengguna` | Kelola Akun Users | `Pages/Admin/Users/Index.vue` |
| POST | `/admin/pengguna/{id}/reset-password` | Reset Password User | Redirect Back |
| PATCH | `/admin/pengguna/{id}/toggle-active` | Aktifkan/Nonaktifkan User | Redirect Back |
| CRUD | `/admin/jenis-surat` | Kelola Jenis Surat, Schema, Requirement | `Pages/Admin/LetterTypes/Index.vue` |
| GET / PUT | `/admin/desa/identitas-wilayah` | Identitas Desa & Dusun | `Pages/Admin/Village/Identity.vue` |
| GET / PUT | `/admin/desa/visi-misi` | Visi Misi & Perangkat Desa | `Pages/Admin/Village/Government.vue` |

---

## 9. Template Surat dan PDF

`letter_types.template` berisi teks surat dengan placeholder `{{nama}}`. Render dengan penggantian string aman (escape HTML), lalu bungkus dengan layout blade `resources/views/pdf/letter.blade.php`.

Placeholder yang didukung:
`{{nama}}`, `{{nik}}`, `{{tempat_tanggal_lahir}}`, `{{jenis_kelamin}}`, `{{agama}}`, `{{status_perkawinan}}`, `{{pekerjaan}}`, `{{alamat}}`, `{{keperluan}}`, `{{nomor_surat}}`, `{{tanggal_surat}}`, `{{nama_desa}}`, `{{kecamatan}}`, `{{kabupaten}}`, `{{nama_penandatangan}}`, `{{jabatan_penandatangan}}`, serta `{{form.<key>}}` untuk isian tambahan.

---

## 10. Definisi Laporan

- Zona waktu `Asia/Jakarta`. Pengajuan dihitung berdasarkan `created_at`.
- Halaman laporan (`/staf/laporan`) menyajikan statistik harian dan agregat periode secara interaktif.
- Ekspor PDF dan Excel mengeksekusi method Controller yang mengembalikan `response()->download()` atau Stream Binary PDF dari Blade template.

---

## 11. Spesifikasi Frontend (Inertia.js Integration)

**Pengelolaan State & Props:**
- Global Props (User login, flash toast message) dikelola melalui Middleware `HandleInertiaRequests.php`.
- Form Submission pada komponen Vue menggunakan `useForm()` dari `@inertiajs/vue-3`. Error validasi `422` dari Laravel FormRequest akan otomatis mengisi object `form.errors`.

**Navigasi:**
- Seluruh navigasi internal Vue menggunakan komponen `<Link href="...">` dari Inertia.

**Sidebar Menu per Peran:**

Warga:
```text
Pengajuan Saya
Ajukan Surat Baru
Data Diri & Akun
```

Staf:
```text
Antrean Pengajuan
Input Layanan Luring
Data Penduduk
Laporan
Info Desa (Potensi & Sarana)
Akun Saya
```

Admin:
```text
Dashboard
Pengajuan (semua)
Input Layanan Luring
Laporan
Data Penduduk
Info Desa (Identitas, Visi Misi, Potensi, Sarana)
Pengaturan (Kelola Akun, Jenis Surat & Template)
Akun Saya
```

**Komponent Reusable Wajib (`resources/js/Components/`):**
- `StatusBadge.vue`: Render warna & label status.
- `DataTable.vue`: Wrapper tabel paginasi data.
- `FormField.vue`: Wrapper input form + error text.
- `FileUpload.vue`: Input berkas + validasi ukuran client side.
- `ConfirmDialog.vue`: Modal konfirmasi aksi sensitif.
- `AppToast.vue`: Pop-up notifikasi untuk flash message.

---

## 12. Keamanan

- Password di-hash (bcrypt/argon bawaan Laravel). Minimal 8 karakter.
- Rate limit pada route `/login` dan `/register` (`throttle:6,1`).
- Otorisasi Controller dan Policy untuk `LetterRequest` dan `RequestAttachment`.
- Mass assignment dilindungi (`$fillable`).
- Unggahan berkas: Simpan di disk privat (`storage/app/private/ attachments/`), diakses lewat Controller yang memeriksa Policy pengguna.
- Protection terhadap CSRF ditangani otomatis oleh middleware CSRF Inertia/Laravel.

---

## 13. Konvensi Kode

**Backend**
- Controller tipis: validasi (FormRequest) → Service → `Inertia::render()`.
- Logika bisnis di `Services/` (`RequestStatusService`, `RegistrationNumberService`, `LetterNumberService`, `LetterRenderService`, `ReportService`).
- PHP backed enum untuk `Role`, `RequestStatus`, `Channel`, `Gender`.
- Operasi multi-tabel dibungkus `DB::transaction`.

**Frontend (Vue 3 / Inertia)**
- HANYA Composition API dengan `<script setup>`. Dilarang Options API.
- Menggunakan `useForm` dari `@inertiajs/vue-3` untuk submit data.
- Komponen Vue bergaya PascalCase.

---

## 14. Fase Pengerjaan dan Kriteria Selesai

Kerjakan berurutan. Berhenti di akhir tiap fase.

**Fase 0 - Setup (Laravel + Inertia)**
- Setup proyek Laravel, install `@inertiajs/vue-3`, Vite, Tailwind CSS v4, `.env.example`, database connection, `HandleInertiaRequests` middleware, layout dasar Vue.
- Selesai jika: Aplikasi bisa dijalankan (`php artisan serve` & `npm run dev`) dan halaman utama Inertia berhasil dirender.

**Fase 1 - Auth dan Peran (Web Session)**
- Migration `users`, Enum Role, Auth controllers (Login, Logout, Register, ChangePassword), middleware `role`, layout Vue per peran, halaman admin kelola pengguna (termasuk toggle active & reset password). Seeder akun `staf@` dan `admin@`.
- Test Feature: Login/Logout session, otorisasi role (akses role lain abort 403), ganti password, reset password admin, user nonaktif ditolak login.

**Fase 2 - Data Penduduk dan Registrasi Warga**
- Migration `hamlets`, `residents`; CRUD backend + Halaman Inertia `/staf/penduduk`; Seeder 20 penduduk fiktif & dusun; `POST /auth/register` (registrasi warga 2 langkah dalam 1 form transaction); Halaman `/warga/profil`; Seeder akun `warga@`.
- Test Feature: CRUD penduduk, validasi NIK 16 digit, registrasi warga (NIK baru/lama matching), restriction edit NIK, verifikasi status oleh staf.

**Fase 3 - Modul Info Desa**
- Migration `village_profiles`, `officials`, `village_potentials`, `facilities`; Admin village management pages; Staf potentials/facilities pages; Halaman publik (`/`, `/profil`, `/potensi`, `/sarana`).
- Test Feature: Staf mendapat 403 saat mengubah profil desa/perangkat desa, publik bisa mengakses tanpa login.

---

**GERBANG DATA WAWANCARA (sebelum Fase 4)**
Agent **dilarang memulai Fase 4** sebelum pemilik proyek menyerahkan data jenis surat, template, persyaratan, dan form schema.

---

**Fase 4 - Jenis Surat**
- Migration `letter_types`, `letter_type_requirements`; CRUD Admin di `/admin/jenis-surat`; Halaman `/layanan`; Builder schema form dinamis.
- Test Feature: Requirement sync, restriction delete jika jenis surat sudah dipakaikan pengajuan, validasi JSON schema.

**Fase 5 - Pengajuan Daring (Warga)**
- Migration `letter_requests`, `request_attachments`, `request_status_logs`; `RegistrationNumberService`; Form pengajuan dinamis di Vue; Detail & download attachment privat.
- Test Feature: Submit pengajuan daring, validasi form schema & requirement file, restriction multiple active request jenis surat sama.

**Fase 6 - Verifikasi, Status, dan Luring (Staf)**
- `RequestStatusService`; Halaman antrean staf `/staf/pengajuan`; Form luring + modal tambah penduduk; Modal ubah status + note; Resubmit form oleh warga.
- Test Feature: Uji semua transisi status valid/invalid, pembuatan log status, pendaftaran pengajuan luring.

**Fase 7 - Generate Surat PDF**
- `LetterNumberService`, `LetterRenderService`, Blade PDF template; Endpoint / action generate-letter & download PDF.
- Test Feature: Nomor surat unik berurutan, penggantian placeholder `{{form.<key>}}`, proteksi download surat yang belum digenerate.

**Fase 8 - Laporan dan Dashboard**
- `ReportService`; Halaman `/staf/laporan` & `/admin/dashboard`; Export PDF & Excel.
- Test Feature: Sinkronisasi summary & rows laporan, filter rentang tanggal, otorisasi dashboard admin.

**Fase 9 - Finalisasi**
- Polish UI, README lengkap, seeder demo akhir, pastikan seluruh Feature Test lulus.

---

## 15. Asumsi dan Keputusan Default

- **A1.** Registrasi warga memakai email + biodata KTP. Akun langsung aktif; `is_verified` di-set `false` sampai staf memverifikasi.
- **A2.** Login berbasis Web Session cookie bawaan Laravel.
- **A3.** `village_profiles` selalu satu baris.
- **A4.** Jenis surat dan template final berasal dari data wawancara.
- **A5.** Format nomor registrasi (`REG-YYYYMMDD-NNNN`) dan nomor surat (`NNN/KODE/BULAN_ROMAWI/TAHUN`).
- **A6.** Pengajuan & penduduk tidak dihapus permanen (soft delete / preserved record).
- **A7.** Tidak ada notifikasi email/WhatsApp (status dipantau via badge di UI).
- **A8.** Peran pimpinan diakomodasi oleh role `admin`.

---

## 16. Di Luar Cakupan (JANGAN dikerjakan)

- REST API JSON & Sanctum Bearer Token (sudah diganti Inertia)
- Tanda tangan digital / QR Code
- Notifikasi Email / WhatsApp / SMS
- Integrasi Sistem Eksternal (Dukcapil, OpenSID)
- Multi-desa / Multi-tenant
- Library UI tambahan di luar Tailwind CSS