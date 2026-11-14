<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WargaLayout from '@/Layouts/WargaLayout.vue';

defineProps({
  requests: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <Head title="Pengajuan Saya" />

  <WargaLayout>
    <template #header>Pengajuan Surat Saya</template>

    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-8 sm:p-10 text-center max-w-xl mx-auto space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-2xs">
          📑
        </div>
        <div>
          <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Selamat Datang di Portal Warga</h3>
          <p class="text-xs sm:text-sm text-slate-400 mt-1.5 leading-relaxed max-w-md mx-auto">
            Anda belum memiliki riwayat pengajuan surat aktif. Formulir pengajuan surat daring mandiri akan tersedia pada fase implementasi layanan warga.
          </p>
        </div>
        <div class="pt-2">
          <Link
            href="/warga/profil"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold transition border border-teal-200/60"
          >
            <span>👤 Periksa Kelengkapan Data Diri</span>
          </Link>
        </div>
      </div>
    </div>
  </WargaLayout>
</template>

