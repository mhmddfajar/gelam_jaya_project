<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const currentLayout = computed(() => {
  return user.value?.role === 'admin' ? AdminLayout : StafLayout;
});

defineProps({
  requests: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <Head title="Antrean Pengajuan Surat" />

  <component :is="currentLayout">
    <template #header>Antrean Layanan Pengajuan</template>

    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-8 sm:p-10 text-center max-w-xl mx-auto space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-3xl mx-auto shadow-2xs">
          📋
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-800">Antrean Pelayanan Surat</h3>
          <p class="text-xs sm:text-sm text-slate-400 mt-1.5 leading-relaxed max-w-md mx-auto">
            Modul pemrosesan antrean verifikasi berkas dan formulir input layanan luring terintegrasi akan diimplementasikan pada fase berikutnya.
          </p>
        </div>
        <div class="pt-2">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs font-semibold text-slate-600">
            <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
            Siap untuk Fase 6
          </span>
        </div>
      </div>
    </div>
  </component>
</template>
