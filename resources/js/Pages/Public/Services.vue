<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    default: () => ({}),
  },
});
</script>

<template>
  <Head title="Panduan Layanan Administrasi Publik" />

  <AppLayout>
    <div class="space-y-12 py-2 sm:py-6 max-w-5xl mx-auto">
      <!-- Header Banner Konsisten (SaaS Modern, Breadcrumb, Gradient Title) -->
      <section class="relative overflow-hidden rounded-3xl py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center">
        <!-- Ambient Background Glows -->
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
            Panduan Layanan
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
              Administrasi Publik
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Ketahui alur pengajuan surat permohonan dinas secara mandiri (daring online) maupun melalui loket kantor desa (luring konvensional) dalam satu sistem terpadu.
          </p>
        </div>
      </section>

      <!-- Dua Jalur Layanan Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Alur Daring -->
        <div class="bg-white rounded-3xl border border-slate-200/70 p-8 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 space-y-6 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shadow-2xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">1. Alur Pengajuan Daring (Mandiri Online)</h3>
            <ol class="text-xs sm:text-sm text-slate-600 space-y-3 list-decimal pl-4 leading-relaxed">
              <li><strong>Registrasi / Masuk:</strong> Warga mendaftarkan akun menggunakan NIK KTP yang valid.</li>
              <li><strong>Pilih Jenis Surat:</strong> Buka portal warga dan tentukan surat dinas yang dibutuhkan.</li>
              <li><strong>Unggah Berkas:</strong> Siapkan dokumen pendukung (KTP/KK) dalam format JPG atau PDF (maks 2MB).</li>
              <li><strong>Pantau Status:</strong> Status pengajuan diperbarui secara langsung (Diajukan, Diproses, Siap Diambil).</li>
              <li><strong>Pengambilan Dokumen:</strong> Ambil surat fisik di kantor desa setelah berstatus <em>Siap Diambil</em> untuk pembubuhan stempel dan tanda tangan basah.</li>
            </ol>
          </div>

          <div class="pt-4 border-t border-slate-100">
            <Link
              href="/register"
              class="w-full py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] transition-transform text-white font-bold text-xs text-center block shadow-xs hover:shadow-md"
            >
              Daftar Akun Warga Sekarang
            </Link>
          </div>
        </div>

        <!-- 2. Alur Luring -->
        <div class="bg-white rounded-3xl border border-slate-200/70 p-8 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 space-y-6 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-2xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">2. Alur Pengajuan Luring (Loket Kantor Desa)</h3>
            <ol class="text-xs sm:text-sm text-slate-600 space-y-3 list-decimal pl-4 leading-relaxed">
              <li><strong>Kunjungi Kantor Desa:</strong> Datang langsung ke loket pelayanan Desa Gelam Jaya pada jam kerja resmi.</li>
              <li><strong>Bawa Dokumen Kependudukan:</strong> Tunjukkan KTP dan Kartu Keluarga asli kepada petugas loket.</li>
              <li><strong>Input Oleh Staf:</strong> Petugas staf desa akan langsung memasukkan data pengajuan Anda ke sistem terpadu.</li>
              <li><strong>Verifikasi Dokumen:</strong> Berkas diverifikasi langsung oleh staf pelayanan di tempat.</li>
              <li><strong>Penerbitan Surat:</strong> Surat dicetak, ditandatangani oleh pejabat desa berwenang, dan distempel dinas.</li>
            </ol>
          </div>

          <div class="pt-4 border-t border-slate-100 text-center">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Jam Operasional: Senin – Jumat (08.00 – 15.00 WIB)</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
