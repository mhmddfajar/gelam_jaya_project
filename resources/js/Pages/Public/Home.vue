<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    default: () => ({}),
  },
  officials: {
    type: Array,
    default: () => [],
  },
  potentials: {
    type: Array,
    default: () => [],
  },
  facilities: {
    type: Array,
    default: () => [],
  },
  stats: {
    type: Object,
    default: () => ({
      total_residents: 40030,
      total_households: 11015,
      total_hamlets: 4,
      area_size_ha: 294.00,
      total_potentials: 5,
      total_facilities: 31,
    }),
  },
});

function getPotentialIcon(category) {
  switch (category) {
    case 'pertanian':
      return 'sprout';
    case 'umkm':
      return 'store';
    case 'perikanan':
      return 'fish';
    case 'peternakan':
      return 'barn';
    case 'wisata':
      return 'compass';
    default:
      return 'sparkles';
  }
}
</script>

<template>
  <Head title="Beranda Resmi Desa Gelam Jaya" />

  <AppLayout>
    <div class="space-y-16 py-2 sm:py-6">
      <!-- Hero Section (Clean, Modern SaaS, Gradient Text, Decorative Blurs) -->
      <section class="relative overflow-hidden rounded-3xl py-14 sm:py-20 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center">
        <!-- Soft Ambient Background Glows -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto space-y-6">
          <div>
            <p class="text-sm md:text-base font-semibold text-teal-600 tracking-wide uppercase mb-2">
              Selamat Datang di Portal Resmi
            </p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
              Pemerintah Desa
              <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
                {{ profile.name || 'Gelam Jaya' }}
              </span>
            </h1>
          </div>

          <p class="text-base sm:text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Kecamatan {{ profile.district || 'Pasar Kemis' }}, {{ profile.regency || 'Kabupaten Tangerang' }}, Provinsi {{ profile.province || 'Banten' }}.
            Pusat pelayanan administrasi terpadu masyarakat dengan komitmen kecepatan, integritas, dan keterbukaan publik.
          </p>

          <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <Link
              href="/login"
              class="px-6 py-3.5 rounded-2xl bg-teal-600 hover:bg-teal-700 active:scale-[0.98] transition-transform text-white font-bold text-sm shadow-md hover:shadow-xl hover:shadow-teal-900/15 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Ajukan Surat Mandiri</span>
            </Link>

            <Link
              href="/profil"
              class="px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-50 active:scale-[0.98] transition-transform text-slate-700 border border-slate-200/90 font-bold text-sm shadow-2xs hover:shadow-md flex items-center justify-center"
            >
              Jelajahi Profil Desa
            </Link>
          </div>
        </div>
      </section>

      <!-- 4 Stat Cards Utama (Data Riil: 40.030 Jiwa, 11.015 KK, 4 Dusun, 294 Ha) -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Penduduk -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 text-center group">
          <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-3 shadow-2xs group-hover:scale-110 transition-transform duration-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <p class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
            {{ Number(stats.total_residents || 40030).toLocaleString('id-ID') }}
          </p>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Total Penduduk (Jiwa)</p>
        </div>

        <!-- 2. Jumlah KK -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 text-center group">
          <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto mb-3 shadow-2xs group-hover:scale-110 transition-transform duration-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <p class="text-2xl sm:text-3xl font-black text-cyan-600 tracking-tight">
            {{ Number(stats.total_households || 11015).toLocaleString('id-ID') }}
          </p>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Kepala Keluarga (KK)</p>
        </div>

        <!-- 3. Wilayah Dusun -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 text-center group">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-2xs group-hover:scale-110 transition-transform duration-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <p class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">
            {{ stats.total_hamlets || 4 }} Dusun
          </p>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Wilayah Administratif</p>
        </div>

        <!-- 4. Luas Wilayah -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 text-center group">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3 shadow-2xs group-hover:scale-110 transition-transform duration-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
          </div>
          <p class="text-2xl sm:text-3xl font-black text-indigo-600 tracking-tight">
            {{ Number(stats.area_size_ha || 294).toLocaleString('id-ID') }} Ha
          </p>
          <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Luas Wilayah Desa</p>
        </div>
      </section>

      <!-- Pelayanan Administrasi Dua Jalur (Daring & Luring) -->
      <section class="bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Sistem Pelayanan Terpadu Satu Pintu
          </h2>
          <p class="text-xs sm:text-sm text-slate-400">
            Dua jalur pengajuan surat administrasi warga yang diproses dalam satu pangkalan data terintegrasi.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Card Daring -->
          <div class="p-8 rounded-3xl bg-gradient-to-br from-teal-50/60 to-cyan-50/40 border border-teal-100 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-teal-900/5 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
              <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white flex items-center justify-center shadow-sm shadow-teal-600/30">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900">Layanan Daring (Mandiri Online)</h3>
              <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Warga mendaftar akun berbasis NIK, mengajukan surat permohonan dari rumah, mengunggah dokumen persyaratan, dan memantau status secara langsung melalui gawai.
              </p>
            </div>
            <div class="pt-2">
              <Link
                href="/register"
                class="inline-flex items-center text-xs font-bold text-teal-700 hover:text-teal-900 transition"
              >
                Daftar Akun Portal Warga
              </Link>
            </div>
          </div>

          <!-- Card Luring -->
          <div class="p-8 rounded-3xl bg-slate-50/80 border border-slate-200/80 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-slate-900/5 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
              <div class="w-12 h-12 rounded-2xl bg-slate-800 text-white flex items-center justify-center shadow-sm shadow-slate-800/30">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900">Layanan Luring (Loket Kantor Desa)</h3>
              <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Warga yang datang langsung ke kantor desa tetap dilayani ramah oleh staf loket pelayanan. Pengajuan diinput ke sistem yang sama dan siap diproses seketika.
              </p>
            </div>
            <div class="pt-2">
              <span class="inline-flex items-center text-xs font-semibold text-slate-500 gap-1">
                <span>🕒</span>
                <span>Jam Layanan: Senin – Jumat (08.00 – 16.00 WIB)</span>
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Visi & Misi Card (Dark Premium Slate-900 to Teal-950) -->
      <section v-if="profile.vision" class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 sm:p-12 shadow-xl">
        <!-- Giant Decorative Quote Watermark -->
        <div class="absolute -right-6 -bottom-10 text-white/[0.04] text-[180px] font-serif font-black select-none pointer-events-none">
          “
        </div>

        <div class="relative z-10 max-w-3xl space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-400/30 text-teal-300 text-xs font-bold uppercase tracking-wider">
            <span>📜</span>
            <span>Visi Pembangunan Desa</span>
          </div>

          <blockquote class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-relaxed text-teal-50 italic">
            "{{ profile.vision }}"
          </blockquote>

          <p class="text-xs sm:text-sm text-slate-400 leading-relaxed pt-2">
            Pemerintah Desa {{ profile.name || 'Gelam Jaya' }} berkomitmen melayani seluruh masyarakat dengan prima melalui transformasi tata kelola yang inklusif dan akuntabel.
          </p>

          <div class="pt-4 border-t border-slate-800 flex items-center justify-between flex-wrap gap-4">
            <span class="text-xs text-slate-400">
              Kantor Desa Gelam Jaya • Jl. Villa Regency Tangerang II No. 01
            </span>
            <Link href="/profil" class="text-xs font-bold text-teal-400 hover:text-teal-300 transition">
              Baca Misi & Program Selengkapnya
            </Link>
          </div>
        </div>
      </section>

      <!-- Potensi Unggulan Preview (Cards with SVG Gradients, No Broken Image) -->
      <section v-if="potentials.length > 0" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Potensi Unggulan Desa</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Sektor ekonomi, komoditas, dan UMKM yang mendorong kemandirian warga.</p>
          </div>
          <Link href="/potensi" class="text-xs font-bold text-teal-600 hover:text-teal-700">
            Lihat Semua ({{ stats.total_potentials || potentials.length }})
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="item in potentials"
            :key="item.id"
            class="bg-white rounded-2xl border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-teal-900/5 overflow-hidden flex flex-col group"
          >
            <!-- Card Image or Beautiful SVG Fallback Header -->
            <div class="h-44 overflow-hidden relative">
              <img
                v-if="item.photo_path"
                :src="item.photo_path"
                :alt="item.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div
                v-else
                class="w-full h-full bg-gradient-to-br from-teal-50 via-slate-50 to-emerald-50/70 flex items-center justify-center p-6 border-b border-slate-100"
              >
                <!-- SVG Icon Theme by Category -->
                <div class="w-16 h-16 rounded-2xl bg-white border border-teal-100/80 shadow-xs flex items-center justify-center text-teal-600 group-hover:scale-110 transition-transform duration-300">
                  <svg v-if="getPotentialIcon(item.category) === 'sprout'" class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V6m0 0l-4 4m4-4l4 4m-8 9a4 4 0 008 0" />
                  </svg>
                  <svg v-else-if="getPotentialIcon(item.category) === 'store'" class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                  <svg v-else-if="getPotentialIcon(item.category) === 'fish'" class="w-8 h-8 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <svg v-else class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                </div>
              </div>

              <span class="absolute top-3 right-3 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-white/95 text-teal-800 shadow-2xs border border-slate-200/60 backdrop-blur-xs uppercase tracking-wider">
                {{ item.category }}
              </span>
            </div>

            <!-- Card Content -->
            <div class="p-6 flex-1 flex flex-col justify-between space-y-3">
              <div>
                <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-teal-700 transition">
                  {{ item.title }}
                </h3>
                <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                  {{ item.description }}
                </p>
              </div>

              <p class="text-xs text-slate-400 pt-3 border-t border-slate-100 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ item.location || 'Wilayah Desa Gelam Jaya' }}</span>
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Section Perangkat Desa (Modern Badge Inisial, Nama, Jabatan, Periode) -->
      <section v-if="officials.length > 0" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pemerintahan Desa</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Aparatur yang siap mengabdi dan melayani kebutuhan administrasi warga.</p>
          </div>
          <Link href="/profil" class="text-xs font-bold text-teal-600 hover:text-teal-700">
            Struktur Lengkap (12 Aparatur)
          </Link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <div
            v-for="official in officials"
            :key="official.id"
            class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-teal-900/5 text-center flex flex-col items-center group"
          >
            <!-- Modern Avatar Badge with Ring Accent -->
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-50 border-2 border-teal-100/90 overflow-hidden mb-4 flex items-center justify-center text-xl font-black text-teal-700 shadow-2xs group-hover:scale-105 transition-transform duration-300">
              <img v-if="official.photo_path" :src="official.photo_path" :alt="official.name" class="w-full h-full object-cover" />
              <span v-else class="tracking-wider">{{ official.name.charAt(0) }}</span>
            </div>

            <h3 class="font-bold text-slate-900 text-sm leading-snug group-hover:text-teal-700 transition">
              {{ official.name }}
            </h3>

            <p class="text-xs text-teal-600 font-bold mt-1">
              {{ official.position }}
            </p>

            <div class="mt-3 pt-2.5 border-t border-slate-100 w-full flex items-center justify-between text-[11px] text-slate-400">
              <span>Masa Bakti</span>
              <span class="font-mono font-medium text-slate-600">
                {{ official.period_start || '2021' }} – {{ official.period_end || '2027' }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Section Lokasi & Layanan Kantor Desa (Google Maps Embed, Alamat, Jam Layanan) -->
      <section class="bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-100 pb-5">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold mb-2">
              <span>📍</span>
              <span>Lokasi & Kontak Resmi</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              Lokasi & Layanan Kantor Desa
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
              Kunjungi loket kantor pelayanan terpadu atau temukan lokasi resmi Pemerintah Desa Gelam Jaya.
            </p>
          </div>

          <a
            href="https://maps.google.com/?q=Kantor+Desa+Gelam+Jaya+Pasar+Kemis+Tangerang"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200/80 text-xs font-bold transition shadow-2xs self-start md:self-auto"
          >
            <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Buka di Google Maps</span>
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <!-- Detail Alamat & Jam Operasional (5 Kolom) -->
          <div class="lg:col-span-5 space-y-6">
            <!-- Alamat Kantor -->
            <div class="space-y-2">
              <span class="text-xs font-bold text-teal-600 uppercase tracking-wider block">Alamat Kantor Desa</span>
              <h3 class="text-lg font-bold text-slate-900">
                Kantor Pemerintah Desa Gelam Jaya
              </h3>
              <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                {{ profile.address || 'Jl. Villa Regency Tangerang II No. 01 Desa Gelam Jaya, Kec. Pasar Kemis, Kab. Tangerang, Banten 15560' }}
              </p>
            </div>

            <!-- Jam Layanan -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2.5">
              <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Jam Operasional Pelayanan</span>
              </div>
              <div class="space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Senin – Jumat</span>
                  <span class="font-extrabold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200/60 font-mono">
                    08.00 – 16.00 WIB
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Sabtu, Minggu & Libur Nasional</span>
                  <span class="text-slate-400 font-medium">Tutup</span>
                </div>
              </div>
            </div>

            <!-- Kontak & Info Tambahan -->
            <div class="grid grid-cols-2 gap-3 text-xs">
              <div class="p-3 rounded-xl bg-teal-50/50 border border-teal-100/70">
                <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider block">Telepon Kantor</span>
                <span class="font-bold text-slate-800 font-mono mt-0.5 block">{{ profile.phone || '(021) 5900000' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-cyan-50/50 border border-cyan-100/70">
                <span class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider block">Email Resmi</span>
                <span class="font-bold text-slate-800 mt-0.5 block truncate">{{ profile.email || 'kantor@gelamjaya.desa.id' }}</span>
              </div>
            </div>

            <div class="pt-2 flex flex-wrap gap-3">
              <Link
                href="/login"
                class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs shadow-sm hover:shadow-md transition-all flex items-center justify-center"
              >
                Masuk Layanan Mandiri
              </Link>
              <Link
                href="/profil"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition flex items-center justify-center"
              >
                Lihat Profil Lengkap
              </Link>
            </div>
          </div>

          <!-- Peta Embed Google Maps (7 Kolom) -->
          <div class="lg:col-span-7 rounded-2xl overflow-hidden border border-slate-200/80 shadow-2xs min-h-[320px] sm:min-h-[380px] bg-slate-100 relative">
            <iframe
              src="https://maps.google.com/maps?q=Kantor+Desa+Gelam+Jaya+Pasar+Kemis+Tangerang&t=&z=16&ie=UTF8&iwloc=&output=embed"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Peta Lokasi Kantor Desa Gelam Jaya"
              class="w-full h-full min-h-[320px] sm:min-h-[380px]"
            ></iframe>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
