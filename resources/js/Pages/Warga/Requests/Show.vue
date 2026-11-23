<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import WargaLayout from '@/Layouts/WargaLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  letterRequest: {
    type: Object,
    required: true,
  },
});

const req = computed(() => props.letterRequest);

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(d);
};

// Pipeline status steps
const statusSteps = [
  { key: 'diajukan', label: 'Diajukan' },
  { key: 'diverifikasi', label: 'Diverifikasi' },
  { key: 'diproses', label: 'Diproses' },
  { key: 'siap_diambil', label: 'Siap Diambil' },
  { key: 'selesai', label: 'Selesai' },
];

const currentStepIndex = computed(() => {
  const currentKey = req.value.status?.value || req.value.status;
  if (currentKey === 'ditolak') return -1;
  if (currentKey === 'perlu_perbaikan') return 0;
  return statusSteps.findIndex(s => s.key === currentKey);
});
</script>

<template>
  <Head :title="`Detail Pengajuan ${req.registration_no}`" />

  <WargaLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Back Link & Header -->
      <div class="space-y-1">
        <Link
          href="/warga/pengajuan"
          class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Kembali ke Daftar Pengajuan</span>
        </Link>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
          <div>
            <div class="flex items-center gap-2.5">
              <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                {{ req.registration_no }}
              </span>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                {{ req.letter_type?.name }}
              </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
              Diajukan pada {{ formatDate(req.created_at) }} via Jalur {{ req.channel?.label || req.channel }}
            </p>
          </div>

          <div>
            <StatusBadge
              :status="req.status?.value || req.status"
              :label="req.status?.label"
            />
          </div>
        </div>
      </div>

      <!-- Alert Callouts for Actionable States -->
      <!-- State 1: Perlu Perbaikan -->
      <div
        v-if="(req.status?.value || req.status) === 'perlu_perbaikan'"
        class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2 animate-fade-in"
      >
        <div class="flex items-center gap-2 font-bold text-sm text-amber-800">
          <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span>Pengajuan Memerlukan Perbaikan Berkas</span>
        </div>
        <p class="text-xs sm:text-sm text-amber-800 leading-relaxed">
          Catatan Petugas: <em>"{{ req.status_note || 'Mohon periksa kembali berkas lampiran yang diunggah.' }}"</em>
        </p>
      </div>

      <!-- State 2: Siap Diambil -->
      <div
        v-if="(req.status?.value || req.status) === 'siap_diambil'"
        class="p-4 sm:p-5 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 space-y-2 animate-fade-in"
      >
        <div class="flex items-center gap-2 font-bold text-sm text-teal-800">
          <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Dokumen Surat Selesai Diproses & Siap Diambil</span>
        </div>
        <p class="text-xs sm:text-sm text-teal-800 leading-relaxed">
          Naskah surat dinas telah selesai dicetak. Silakan datang ke loket pelayanan Kantor Desa Gelam Jaya pada jam kerja operasional untuk pembubuhan tanda tangan basah dan stempel dinas.
        </p>
      </div>

      <!-- State 3: Ditolak -->
      <div
        v-if="(req.status?.value || req.status) === 'ditolak'"
        class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 space-y-2 animate-fade-in"
      >
        <div class="flex items-center gap-2 font-bold text-sm text-rose-800">
          <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Permohonan Surat Ditolak</span>
        </div>
        <p class="text-xs sm:text-sm text-rose-800 leading-relaxed">
          Alasan Penolakan: <em>"{{ req.status_note || 'Tidak memenuhi ketentuan administrasi.' }}"</em>
        </p>
      </div>

      <!-- Visual Progress Tracker -->
      <div
        v-if="(req.status?.value || req.status) !== 'ditolak'"
        class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4"
      >
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pelacakan Alur Pelayanan</h2>

        <div class="grid grid-cols-5 gap-2 text-center text-xs">
          <div
            v-for="(step, idx) in statusSteps"
            :key="step.key"
            class="space-y-2"
          >
            <div
              class="h-2 rounded-full transition-all"
              :class="idx <= currentStepIndex ? 'bg-emerald-500' : 'bg-slate-100'"
            ></div>
            <span
              class="block text-[11px] font-bold"
              :class="idx <= currentStepIndex ? 'text-emerald-700' : 'text-slate-400'"
            >
              {{ step.label }}
            </span>
          </div>
        </div>
      </div>

      <!-- Section 1: Informasi Permohonan -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
          1. Data Pokok Pengajuan
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
          <div>
            <span class="text-slate-400 font-medium block">Nomor Registrasi:</span>
            <span class="font-mono font-bold text-slate-900">{{ req.registration_no }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Jenis Surat Dinas:</span>
            <span class="font-bold text-slate-900">[{{ req.letter_type?.code }}] {{ req.letter_type?.name }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Nama Pemohon (KTP):</span>
            <span class="font-bold text-slate-800">{{ req.resident?.name }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Nomor Induk Kependudukan (NIK):</span>
            <span class="font-mono text-slate-800">{{ req.resident?.nik }}</span>
          </div>
          <div class="sm:col-span-2">
            <span class="text-slate-400 font-medium block">Keperluan Permohonan:</span>
            <p class="font-semibold text-slate-900 bg-slate-50 p-3 rounded-xl border border-slate-100 mt-1">
              {{ req.purpose }}
            </p>
          </div>
        </div>
      </div>

      <!-- Section 2: Data Formulir Khusus (Jika Ada) -->
      <div
        v-if="req.form_data && Object.keys(req.form_data).length > 0"
        class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4"
      >
        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
          2. Rincian Isian Formulir
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
          <div
            v-for="(val, key) in req.form_data"
            :key="key"
            class="p-3 rounded-xl bg-slate-50 border border-slate-100"
          >
            <span class="text-[11px] font-bold text-slate-400 block uppercase">{{ key.replace(/_/g, ' ') }}</span>
            <span class="font-semibold text-slate-800 mt-0.5 block">{{ val || '-' }}</span>
          </div>
        </div>
      </div>

      <!-- Section 3: Berkas Persyaratan Terlampir -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
          <span>3. Berkas Persyaratan Terlampir ({{ req.attachments?.length || 0 }})</span>
          <span class="text-xs font-normal text-slate-400">Tersimpan di arsip privat</span>
        </h2>

        <div v-if="req.attachments && req.attachments.length > 0" class="space-y-2.5">
          <div
            v-for="att in req.attachments"
            :key="att.id"
            class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3 text-xs sm:text-sm"
          >
            <div class="flex items-center gap-3 truncate">
              <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </span>
              <div class="truncate">
                <p class="font-bold text-slate-800 truncate">{{ att.original_name }}</p>
                <p class="text-[11px] text-slate-400">
                  {{ att.requirement?.name ? att.requirement.name + ' • ' : '' }}{{ att.size_kb }} KB
                </p>
              </div>
            </div>

            <!-- Download Button -->
            <a
              :href="`/warga/pengajuan/${req.id}/attachments/${att.id}`"
              target="_blank"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-emerald-50 text-emerald-700 font-bold text-xs border border-slate-200 hover:border-emerald-200 transition shadow-2xs shrink-0"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              <span>Unduh / Lihat</span>
            </a>
          </div>
        </div>

        <p v-else class="text-xs text-slate-400 italic">
          Tidak ada berkas lampiran yang diunggah.
        </p>
      </div>

      <!-- Section 4: Riwayat Status & Catatan Petugas -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
          4. Riwayat & Log Perkembangan Pengajuan
        </h2>

        <div v-if="req.status_logs && req.status_logs.length > 0" class="space-y-4">
          <div
            v-for="(log, lIdx) in req.status_logs"
            :key="log.id"
            class="flex items-start gap-3 text-xs"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
            <div class="flex-1 space-y-1">
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 uppercase tracking-wide">
                  {{ log.to_status_label || log.to_status }}
                </span>
                <span class="text-slate-400 text-[11px]">{{ formatDate(log.created_at) }}</span>
              </div>
              <p v-if="log.note" class="text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                {{ log.note }}
              </p>
              <span class="text-[10px] text-slate-400 block">
                Diperbarui oleh: {{ log.changer?.name || 'Sistem' }}
              </span>
            </div>
          </div>
        </div>

        <p v-else class="text-xs text-slate-400 italic">
          Belum ada riwayat perubahan status.
        </p>
      </div>
    </div>
  </WargaLayout>
</template>
