<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    default: () => ({}),
  },
  letterTypes: {
    type: Array,
    default: () => [],
  },
});

const searchQuery = ref('');
const selectedLetter = ref(null);

const filteredLetterTypes = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.letterTypes;
  }
  const q = searchQuery.value.toLowerCase().trim();
  return props.letterTypes.filter(
    (item) =>
      item.code.toLowerCase().includes(q) ||
      item.name.toLowerCase().includes(q) ||
      (item.description && item.description.toLowerCase().includes(q))
  );
});

const openDetailModal = (item) => {
  selectedLetter.value = item;
};
</script>

<template>
  <Head title="Katalog Layanan Surat & Panduan Administrasi" />

  <AppLayout>
    <div class="space-y-12 py-2 sm:py-6 max-w-5xl mx-auto">
      <!-- Header Banner Konsisten (SaaS Modern, Breadcrumb, Clean Soft Light) -->
      <section class="relative overflow-hidden rounded-3xl py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center">
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
            Katalog Layanan &
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
              Panduan Surat Desa
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Daftar lengkap layanan surat keterangan resmi Desa Gelam Jaya beserta rincian syarat berkas yang harus disiapkan sebelum mengajukan permohonan.
          </p>
        </div>
      </section>

      <!-- Dua Jalur Layanan Grid (Daring vs Luring) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Alur Daring -->
        <div class="bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-8 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 space-y-6 flex flex-col justify-between">
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
        <div class="bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-8 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 space-y-6 flex flex-col justify-between">
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
              <span>Jam Operasional: Senin – Jumat (08.00 – 16.00 WIB)</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Section: Katalog Master Jenis Surat & Persyaratan -->
      <section class="space-y-6 pt-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
              <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                Daftar Jenis Surat Tersedia
              </h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
              Pilih salah satu surat dinas di bawah ini untuk melihat rincian dokumen persyaratan dan formulir yang diperlukan.
            </p>
          </div>

          <!-- Search Input -->
          <div class="relative w-full sm:w-72">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari surat (SKU, Domisili, SKTM)..."
              class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition shadow-2xs"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <!-- Cards Grid Katalog Jenis Surat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div
            v-for="(item, index) in filteredLetterTypes"
            :key="item.id"
            class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs hover:shadow-md hover:border-teal-200 transition-all duration-300 flex flex-col justify-between group"
          >
            <div class="space-y-4">
              <!-- Header Card (Kode & Nama) -->
              <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200/80">
                    {{ item.code }}
                  </span>
                  <h3 class="text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                    {{ item.name }}
                  </h3>
                </div>

                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  Tersedia
                </span>
              </div>

              <!-- Deskripsi Surat -->
              <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                {{ item.description || 'Surat permohonan administrasi resmi warga Desa Gelam Jaya.' }}
              </p>

              <!-- Preview Syarat Berkas -->
              <div class="pt-2 border-t border-slate-100 space-y-2">
                <span class="text-[11px] font-bold text-slate-600 block uppercase tracking-wider">
                  Syarat Berkas ({{ item.requirements ? item.requirements.length : 0 }} Dokumen):
                </span>
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="(req, rIdx) in (item.requirements || []).slice(0, 3)"
                    :key="rIdx"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] bg-slate-50 border border-slate-200 text-slate-700 font-medium"
                  >
                    <svg class="w-3 h-3 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ req }}</span>
                  </span>
                  <span
                    v-if="item.requirements && item.requirements.length > 3"
                    class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] bg-slate-100 text-slate-500 font-bold"
                  >
                    +{{ item.requirements.length - 3 }} lainnya
                  </span>
                </div>
              </div>

              <!-- Preview Form Fields -->
              <div v-if="item.form_fields && item.form_fields.length > 0" class="pt-1">
                <span class="text-[11px] text-slate-400 font-medium">
                  Memerlukan {{ item.form_fields.length }} isian data khusus tambahan.
                </span>
              </div>
            </div>

            <!-- Card Footer / Actions -->
            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between gap-3">
              <button
                type="button"
                @click="openDetailModal(item)"
                class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline flex items-center gap-1"
              >
                <span>Lihat Syarat Lengkap</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>

              <Link
                href="/warga/pengajuan"
                class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition active:scale-[0.98] shadow-2xs"
              >
                Ajukan Surat
              </Link>
            </div>
          </div>
        </div>

        <div
          v-if="filteredLetterTypes.length === 0"
          class="bg-white rounded-3xl p-10 border border-slate-200/80 text-center text-slate-400 space-y-2"
        >
          <p class="font-bold text-slate-700">Surat tidak ditemukan</p>
          <p class="text-xs text-slate-400">Tidak ada jenis surat yang cocok dengan kata kunci "{{ searchQuery }}".</p>
        </div>
      </section>
    </div>

    <!-- Modal Syarat Lengkap & Formulir -->
    <div
      v-if="selectedLetter"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition"
    >
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-xl border border-slate-200 space-y-5 animate-smooth-enter">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200">
              {{ selectedLetter.code }}
            </span>
            <h2 class="text-base sm:text-lg font-black text-slate-900">
              {{ selectedLetter.name }}
            </h2>
          </div>
          <button
            type="button"
            @click="selectedLetter = null"
            class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1 text-xs sm:text-sm">
          <p class="text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
            {{ selectedLetter.description || 'Surat permohonan dinas resmi Desa Gelam Jaya.' }}
          </p>

          <!-- Berkas Persyaratan -->
          <div class="space-y-2">
            <span class="font-bold text-slate-800 block text-xs uppercase tracking-wider">
              Dokumen Persyaratan yang Harus Diunggah / Dibawa:
            </span>
            <div class="space-y-1.5">
              <div
                v-for="(req, i) in (selectedLetter.requirements || [])"
                :key="i"
                class="flex items-center gap-2.5 p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/70 text-slate-800 font-medium"
              >
                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                  {{ i + 1 }}
                </span>
                <span>{{ req }}</span>
              </div>
            </div>
          </div>

          <!-- Kolom Input Formulir -->
          <div v-if="selectedLetter.form_fields && selectedLetter.form_fields.length > 0" class="space-y-2 pt-2 border-t border-slate-100">
            <span class="font-bold text-slate-800 block text-xs uppercase tracking-wider">
              Data yang Akan Ditanyakan pada Formulir:
            </span>
            <div class="space-y-1.5">
              <div
                v-for="(field, fIdx) in selectedLetter.form_fields"
                :key="fIdx"
                class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs"
              >
                <span class="font-semibold text-slate-800">{{ field.label }}</span>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-200/80 text-slate-600">
                  {{ field.type }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
          <Link
            href="/warga/pengajuan"
            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition"
          >
            Masuk & Ajukan Sekarang
          </Link>
          <button
            type="button"
            @click="selectedLetter = null"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
