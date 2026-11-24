<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => (user.value?.role === 'admin' ? AdminLayout : StafLayout));

const props = defineProps({
  letterRequest: {
    type: Object,
    required: true,
  },
  allowedStatuses: {
    type: Array,
    default: () => [],
  },
});

const req = computed(() => props.letterRequest);
const resident = computed(() => req.value.resident);

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

const formatDateOnly = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(d);
};

// Form for changing status
const statusForm = useForm({
  status: '',
  note: '',
});

const selectedTarget = computed(() => {
  return props.allowedStatuses.find(s => s.value === statusForm.status);
});

const isNoteRequired = computed(() => {
  return statusForm.status === 'perlu_perbaikan' || statusForm.status === 'ditolak';
});

const submitStatusChange = () => {
  statusForm.post(`/staf/pengajuan/${req.value.id}/status`, {
    preserveScroll: true,
    onSuccess: () => {
      statusForm.reset();
    },
  });
};

// Form for quick resident verification
const verifyResidentForm = useForm({});
const verifyResident = () => {
  if (!resident.value?.id) return;
  verifyResidentForm.post(`/staf/penduduk/${resident.value.id}/verify`, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="`Detail Pengajuan ${req.registration_no}`" />

  <component :is="currentLayout">
    <template #header>Detail Permohonan Surat</template>

    <div class="max-w-5xl mx-auto space-y-6">
      <!-- Back Link & Header -->
      <div class="space-y-1">
        <Link
          href="/staf/pengajuan"
          class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 transition"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Kembali ke Antrean Pengajuan</span>
        </Link>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                {{ req.registration_no }}
              </span>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                {{ req.letter_type?.name }}
              </h1>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold"
                :class="(req.channel?.value || req.channel) === 'daring'
                  ? 'bg-sky-50 text-sky-700 border border-sky-200'
                  : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
              >
                {{ (req.channel?.value || req.channel) === 'daring' ? '🌐 Daring' : '🏢 Luring' }}
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
              Didaftarkan pada {{ formatDate(req.created_at) }}
              <span v-if="req.submitter">oleh {{ req.submitter.name }} ({{ req.submitter.role?.label || req.submitter.role }})</span>
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

      <!-- Warning Callout: Unverified Resident Data -->
      <div
        v-if="resident && !resident.is_verified"
        class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fade-in"
      >
        <div class="flex items-start gap-3">
          <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
            ⚠️
          </span>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="font-bold text-sm text-amber-900">Data diri belum terverifikasi</h3>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900">
                Perhatian Petugas
              </span>
            </div>
            <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
              Pemohon mendaftar secara mandiri lewat portal web dan data kependudukannya belum diverifikasi dengan KTP fisik/asli.
            </p>
          </div>
        </div>

        <button
          type="button"
          @click="verifyResident"
          :disabled="verifyResidentForm.processing"
          class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-2xs transition shrink-0 disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ verifyResidentForm.processing ? 'Memverifikasi...' : 'Verifikasi Data Warga' }}</span>
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- 1. Data Pemohon -->
          <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
              <span>1. Data Kependudukan Pemohon</span>
              <span
                v-if="resident?.is_verified"
                class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100"
              >
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
                Terverifikasi
              </span>
            </h2>

            <div v-if="resident" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div>
                <span class="text-slate-400 font-medium block">Nama Lengkap (KTP):</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ resident.name }}</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Nomor Induk Kependudukan (NIK):</span>
                <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ resident.nik }}</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Nomor Kartu Keluarga (KK):</span>
                <span class="font-mono text-slate-700 block">{{ resident.family_card_no || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Tempat, Tanggal Lahir:</span>
                <span class="font-medium text-slate-800 block">
                  {{ resident.birth_place }}, {{ formatDateOnly(resident.birth_date) }}
                </span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Jenis Kelamin:</span>
                <span class="font-medium text-slate-800 block">
                  {{ resident.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Agama & Status Perkawinan:</span>
                <span class="font-medium text-slate-800 block">
                  {{ resident.religion }} • {{ resident.marital_status }}
                </span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pekerjaan:</span>
                <span class="font-medium text-slate-800 block">{{ resident.occupation || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Wilayah Dusun:</span>
                <span class="font-medium text-slate-800 block">
                  {{ resident.hamlet?.name ? 'Dusun ' + resident.hamlet.name : '-' }}
                </span>
              </div>
              <div class="sm:col-span-2">
                <span class="text-slate-400 font-medium block">Alamat Domisili:</span>
                <span class="font-medium text-slate-800 block">
                  {{ resident.address }} (RT {{ resident.rt }} / RW {{ resident.rw }})
                </span>
              </div>
            </div>
          </div>

          <!-- 2. Data Pengajuan & Form Khusus -->
          <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
              2. Data Pengajuan & Isian Formulir
            </h2>

            <div class="space-y-4 text-xs">
              <div>
                <span class="text-slate-400 font-medium block">Keperluan Surat:</span>
                <p class="font-bold text-slate-900 bg-slate-50 p-3 rounded-xl border border-slate-100 mt-1 leading-relaxed">
                  {{ req.purpose }}
                </p>
              </div>

              <!-- Dynamic form fields -->
              <div v-if="req.form_data && Object.keys(req.form_data).length > 0">
                <span class="text-slate-400 font-medium block mb-2">Rincian Tambahan:</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div
                    v-for="(val, key) in req.form_data"
                    :key="key"
                    class="p-3 rounded-xl bg-slate-50 border border-slate-100"
                  >
                    <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider">
                      {{ key.replace(/_/g, ' ') }}
                    </span>
                    <span class="font-bold text-slate-800 mt-0.5 block text-xs">
                      {{ val || '-' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Status Note Terakhir -->
              <div v-if="req.status_note" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="text-[11px] font-bold text-slate-500 uppercase block">Catatan Terakhir:</span>
                <p class="text-xs text-slate-700 italic mt-0.5">
                  "{{ req.status_note }}"
                </p>
              </div>
            </div>
          </div>

          <!-- 3. Berkas Persyaratan Terlampir -->
          <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
              <span>3. Berkas Lampiran Persyaratan ({{ req.attachments?.length || 0 }})</span>
              <span class="text-[11px] text-slate-400">Penyimpanan privat terproteksi</span>
            </h2>

            <div v-if="req.attachments && req.attachments.length > 0" class="space-y-2.5">
              <div
                v-for="att in req.attachments"
                :key="att.id"
                class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3 text-xs"
              >
                <div class="flex items-center gap-3 truncate">
                  <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xs shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                  </span>
                  <div class="truncate">
                    <p class="font-bold text-slate-800 truncate">{{ att.original_name }}</p>
                    <p class="text-[11px] text-slate-500">
                      {{ att.requirement?.name ? att.requirement.name + ' • ' : '' }}{{ att.size_kb }} KB
                    </p>
                  </div>
                </div>

                <!-- Tombol Unduh Privat -->
                <a
                  :href="`/staf/attachments/${att.id}/download`"
                  target="_blank"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-teal-50 text-teal-800 font-bold text-xs border border-slate-200 hover:border-teal-300 transition shadow-2xs shrink-0"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  <span>Unduh</span>
                </a>
              </div>
            </div>

            <div v-else class="text-center py-6 text-slate-400 text-xs italic">
              Tidak ada berkas lampiran yang diunggah (layanan luring tanpa berkas digital).
            </div>
          </div>
        </div>

        <!-- Right 1 Col: Status Controls & Logs -->
        <div class="space-y-6">
          <!-- Control Panel Ubah Status -->
          <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
              <span>Kontrol Status Layanan</span>
              <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
            </h2>

            <!-- Terminal status alert -->
            <div
              v-if="(req.status?.value || req.status) === 'selesai' || (req.status?.value || req.status) === 'ditolak'"
              class="p-3.5 rounded-xl text-xs bg-slate-50 border border-slate-200 text-slate-600 leading-relaxed"
            >
              <span class="font-bold text-slate-800 block mb-0.5">Status Akhir (Final)</span>
              Pengajuan ini telah berada pada status final
              <strong>({{ req.status?.label || req.status }})</strong> dan tidak dapat diubah lagi.
            </div>

            <!-- Active Status Transition Form -->
            <form
              v-else-if="allowedStatuses.length > 0"
              @submit.prevent="submitStatusChange"
              class="space-y-4"
            >
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Pilih Status Baru:
                </label>
                <div class="space-y-2">
                  <label
                    v-for="target in allowedStatuses"
                    :key="target.value"
                    class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition text-xs font-medium"
                    :class="statusForm.status === target.value
                      ? 'border-teal-500 bg-teal-50/50 text-teal-900 font-bold shadow-2xs'
                      : 'border-slate-200 hover:bg-slate-50 text-slate-700'"
                  >
                    <div class="flex items-center gap-2.5">
                      <input
                        type="radio"
                        v-model="statusForm.status"
                        :value="target.value"
                        class="text-teal-600 focus:ring-teal-500"
                      />
                      <span>{{ target.label }}</span>
                    </div>
                    <span
                      v-if="target.requireNote"
                      class="text-[10px] text-amber-700 bg-amber-100/80 px-1.5 py-0.5 rounded font-bold"
                    >
                      Wajib Catatan
                    </span>
                  </label>
                </div>
                <p v-if="statusForm.errors.status" class="text-xs text-rose-600 font-medium mt-1">
                  {{ statusForm.errors.status }}
                </p>
              </div>

              <!-- Input Catatan Petugas -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Catatan Petugas / Alasan
                  <span v-if="isNoteRequired" class="text-rose-500">* (Wajib diisi)</span>
                  <span v-else class="text-slate-400 font-normal">(Opsional)</span>:
                </label>
                <textarea
                  v-model="statusForm.note"
                  rows="3"
                  placeholder="Tuliskan catatan perbaikan untuk warga atau alasan penolakan berkas..."
                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                  :class="{ 'border-rose-300 ring-1 ring-rose-200': statusForm.errors.note }"
                ></textarea>
                <p v-if="statusForm.errors.note" class="text-xs text-rose-600 font-medium mt-1">
                  {{ statusForm.errors.note }}
                </p>
              </div>

              <button
                type="submit"
                :disabled="statusForm.processing || !statusForm.status"
                class="w-full py-2.5 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm hover:shadow transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <span v-if="statusForm.processing">Memproses Perubahan...</span>
                <span v-else>Simpan Perubahan Status</span>
              </button>
            </form>

            <div v-else class="text-xs text-slate-400 italic py-2">
              Tidak ada opsi transisi status lanjutan saat ini.
            </div>
          </div>

          <!-- Riwayat Log Status -->
          <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
              Riwayat Perubahan Status ({{ req.status_logs?.length || 0 }})
            </h2>

            <div v-if="req.status_logs && req.status_logs.length > 0" class="space-y-4">
              <div
                v-for="log in req.status_logs"
                :key="log.id"
                class="relative pl-4 border-l-2 border-teal-500/30 text-xs space-y-1"
              >
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-800 uppercase tracking-wide">
                    {{ log.to_status_label || log.to_status }}
                  </span>
                  <span class="text-[10px] text-slate-400">{{ formatDate(log.created_at) }}</span>
                </div>
                <p v-if="log.note" class="text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-[11px] leading-relaxed">
                  "{{ log.note }}"
                </p>
                <span class="text-[10px] text-slate-400 block">
                  Oleh: {{ log.changer?.name || 'Sistem' }}
                </span>
              </div>
            </div>

            <p v-else class="text-xs text-slate-400 italic">
              Belum ada riwayat transisi status.
            </p>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>
