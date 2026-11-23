<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WargaLayout from '@/Layouts/WargaLayout.vue';

const props = defineProps({
  letterTypes: {
    type: Array,
    required: true,
  },
  preselectedType: {
    type: Object,
    default: null,
  },
  resident: {
    type: Object,
    default: () => ({}),
  },
});

const initialTypeId = props.preselectedType ? props.preselectedType.id : (props.letterTypes[0]?.id || '');

const form = useForm({
  letter_type_id: initialTypeId,
  purpose: '',
  form_data: {},
  attachments: {},
});

const selectedType = computed(() => {
  return props.letterTypes.find(t => t.id === Number(form.letter_type_id)) || null;
});

// File upload states
const fileErrors = ref({});
const filePreviews = ref({});

// Watch letter type changes to reset form_data & attachments
watch(
  () => form.letter_type_id,
  (newId) => {
    form.form_data = {};
    form.attachments = {};
    fileErrors.value = {};
    filePreviews.value = {};

    if (selectedType.value?.form_fields) {
      selectedType.value.form_fields.forEach(field => {
        const key = field.name || field.key;
        form.form_data[key] = '';
      });
    }
  },
  { immediate: true }
);

const handleFileChange = (reqId, event) => {
  const file = event.target.files[0];
  fileErrors.value[reqId] = null;

  if (!file) {
    delete form.attachments[reqId];
    delete filePreviews.value[reqId];
    return;
  }

  // Validate format
  const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
  const ext = file.name.split('.').pop().toLowerCase();
  if (!allowedExtensions.includes(ext)) {
    fileErrors.value[reqId] = 'Format berkas harus JPG, JPEG, PNG, atau PDF.';
    delete form.attachments[reqId];
    delete filePreviews.value[reqId];
    event.target.value = '';
    return;
  }

  // Validate max 2MB (2048 KB)
  const sizeKb = file.size / 1024;
  if (sizeKb > 2048) {
    fileErrors.value[reqId] = `Ukuran berkas (${Math.round(sizeKb)} KB) melebihi batas maksimal 2 MB.`;
    delete form.attachments[reqId];
    delete filePreviews.value[reqId];
    event.target.value = '';
    return;
  }

  form.attachments[reqId] = file;
  filePreviews.value[reqId] = {
    name: file.name,
    size: Math.round(sizeKb) + ' KB',
    type: ext.toUpperCase(),
  };
};

const removeFile = (reqId) => {
  delete form.attachments[reqId];
  delete filePreviews.value[reqId];
  delete fileErrors.value[reqId];
};

const submit = () => {
  form.post('/warga/pengajuan', {
    forceFormData: true,
  });
};
</script>

<template>
  <Head title="Buat Permohonan Surat Daring" />

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
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">
          Formulir Pengajuan Surat Mandiri (Daring)
        </h1>
        <p class="text-xs sm:text-sm text-slate-500">
          Isi keperluan surat, lengkapi data spesifik, dan unggah dokumen persyaratan dalam format JPG atau PDF.
        </p>
      </div>

      <!-- Ringkasan Data Pemohon (Readonly Auto-filled from NIK/KTP) -->
      <div class="bg-gradient-to-r from-emerald-50/60 via-teal-50/30 to-white rounded-2xl p-5 border border-emerald-100 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-emerald-200/50 pb-2.5">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </span>
            <div>
              <p class="text-xs font-bold text-emerald-900">Identitas Subjek Pemohon Surat</p>
              <p class="text-[11px] text-emerald-700">Data kependudukan resmi yang tercatat di Kantor Desa Gelam Jaya</p>
            </div>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
            Terhubung NIK
          </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Nama Lengkap:</span>
            <span class="font-bold text-slate-800">{{ resident?.name || '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Nomor Induk Kependudukan:</span>
            <span class="font-mono font-bold text-slate-800">{{ resident?.nik || '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Tempat, Tanggal Lahir:</span>
            <span class="font-medium text-slate-800">{{ resident?.birth_place }}, {{ resident?.birth_date }}</span>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Alamat Domisili KTP:</span>
            <span class="font-medium text-slate-800 truncate block">{{ resident?.address }} RT {{ resident?.rt }}/RW {{ resident?.rw }}</span>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Section 1: Pemilihan Jenis Surat & Keperluan -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">1. Jenis Surat & Keperluan</h2>
            <p class="text-xs text-slate-400 mt-0.5">Tentukan permohonan dinas yang Anda perlukan beserta tujuannya.</p>
          </div>

          <!-- Dropdown Jenis Surat -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              Pilih Jenis Surat Dinas <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.letter_type_id"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-bold text-slate-800 transition"
              :class="{ 'border-rose-300 bg-rose-50/30': form.errors.letter_type_id }"
            >
              <option
                v-for="type in letterTypes"
                :key="type.id"
                :value="type.id"
              >
                [{{ type.code }}] {{ type.name }}
              </option>
            </select>
            <p v-if="form.errors.letter_type_id" class="text-rose-500 text-xs mt-1.5 font-medium leading-relaxed">
              {{ form.errors.letter_type_id }}
            </p>

            <!-- Deskripsi Surat Terpilih -->
            <div v-if="selectedType?.description" class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed">
              <span class="font-bold text-slate-700">Fungsi Surat:</span> {{ selectedType.description }}
            </div>
          </div>

          <!-- Keperluan Surat -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              Keperluan Pengajuan Surat <span class="text-rose-500">*</span>
            </label>
            <input
              type="text"
              v-model="form.purpose"
              placeholder="contoh: Persyaratan Pengajuan KUR Bank BRI / Pendaftaran CPNS / Beasiswa Kampus"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium"
              :class="{ 'border-rose-300 bg-rose-50/30': form.errors.purpose }"
            />
            <p v-if="form.errors.purpose" class="text-rose-500 text-xs mt-1 font-medium">
              {{ form.errors.purpose }}
            </p>
          </div>
        </div>

        <!-- Section 2: Kolom Formulir Dinamis (Khusus Jenis Surat Ini) -->
        <div
          v-if="selectedType?.form_fields && selectedType.form_fields.length > 0"
          class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5 animate-smooth-enter"
        >
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">2. Rincian Isian Khusus</h2>
            <p class="text-xs text-slate-400 mt-0.5">Informasi spesifik yang akan dicantumkan pada naskah surat dinas.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
              v-for="field in selectedType.form_fields"
              :key="field.name || field.key"
              :class="field.type === 'textarea' ? 'sm:col-span-2' : ''"
            >
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                {{ field.label }}
                <span v-if="field.required" class="text-rose-500">*</span>
              </label>

              <!-- Text Input -->
              <input
                v-if="field.type === 'text'"
                type="text"
                v-model="form.form_data[field.name || field.key]"
                :placeholder="`Masukkan ${field.label.toLowerCase()}...`"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
              />

              <!-- Number Input -->
              <input
                v-else-if="field.type === 'number'"
                type="number"
                v-model="form.form_data[field.name || field.key]"
                placeholder="0"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
              />

              <!-- Date Input -->
              <input
                v-else-if="field.type === 'date'"
                type="date"
                v-model="form.form_data[field.name || field.key]"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
              />

              <!-- Textarea Input -->
              <textarea
                v-else-if="field.type === 'textarea'"
                v-model="form.form_data[field.name || field.key]"
                rows="3"
                :placeholder="`Tuliskan ${field.label.toLowerCase()} secara lengkap...`"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
              ></textarea>

              <!-- Select Dropdown -->
              <select
                v-else-if="field.type === 'select'"
                v-model="form.form_data[field.name || field.key]"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
              >
                <option value="">-- Pilih {{ field.label }} --</option>
                <option
                  v-for="(opt, optIdx) in (field.options || [])"
                  :key="optIdx"
                  :value="opt"
                >
                  {{ opt }}
                </option>
              </select>

              <p v-if="form.errors[`form_data.${field.name || field.key}`]" class="text-rose-500 text-xs mt-1 font-medium">
                {{ form.errors[`form_data.${field.name || field.key}`] }}
              </p>
            </div>
          </div>
        </div>

        <!-- Section 3: Unggah Berkas Persyaratan -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">3. Berkas Persyaratan Pendukung</h2>
            <p class="text-xs text-slate-400 mt-0.5">Unggah foto atau scan dokumen asli (format JPG, PNG, PDF | maksimal 2 MB per berkas).</p>
          </div>

          <div class="space-y-4">
            <template v-if="selectedType?.type_requirements && selectedType.type_requirements.length > 0">
              <div
                v-for="req in selectedType.type_requirements"
                :key="req.id"
                class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-50 text-teal-700 font-bold text-xs flex items-center justify-center">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                      </svg>
                    </span>
                    <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ req.name }}</span>
                    <span v-if="req.is_required" class="text-rose-500 text-xs font-bold">* (Wajib)</span>
                  </div>

                  <span
                    v-if="filePreviews[req.id]"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200"
                  >
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Siap Diunggah</span>
                  </span>
                </div>

                <!-- File Input Selector -->
                <div v-if="!filePreviews[req.id]">
                  <input
                    type="file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    @change="handleFileChange(req.id, $event)"
                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer"
                  />
                </div>

                <!-- File Preview Card if Selected -->
                <div
                  v-else
                  class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 text-xs"
                >
                  <div class="flex items-center gap-2.5 truncate">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-bold text-[10px]">
                      {{ filePreviews[req.id].type }}
                    </span>
                    <span class="font-medium text-slate-800 truncate max-w-xs">{{ filePreviews[req.id].name }}</span>
                    <span class="text-slate-400 text-[11px]">({{ filePreviews[req.id].size }})</span>
                  </div>
                  <button
                    type="button"
                    @click="removeFile(req.id)"
                    class="text-xs text-rose-500 hover:text-rose-700 font-bold hover:underline ml-2 shrink-0"
                  >
                    Ganti
                  </button>
                </div>

                <p v-if="fileErrors[req.id]" class="text-rose-500 text-xs font-medium">
                  {{ fileErrors[req.id] }}
                </p>
                <p v-if="form.errors[`attachments.${req.id}`]" class="text-rose-500 text-xs font-medium">
                  {{ form.errors[`attachments.${req.id}`] }}
                </p>
              </div>
            </template>

            <!-- General File Upload if letter type has requirements as string array -->
            <template v-else-if="selectedType?.requirements && selectedType.requirements.length > 0">
              <div
                v-for="(reqName, rIdx) in selectedType.requirements"
                :key="rIdx"
                class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ reqName }}</span>
                    <span class="text-rose-500 text-xs font-bold">* (Wajib)</span>
                  </div>
                </div>

                <div v-if="!filePreviews[rIdx]">
                  <input
                    type="file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    @change="handleFileChange(rIdx, $event)"
                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer"
                  />
                </div>

                <div
                  v-else
                  class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 text-xs"
                >
                  <div class="flex items-center gap-2.5 truncate">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-bold text-[10px]">
                      {{ filePreviews[rIdx].type }}
                    </span>
                    <span class="font-medium text-slate-800 truncate">{{ filePreviews[rIdx].name }}</span>
                    <span class="text-slate-400 text-[11px]">({{ filePreviews[rIdx].size }})</span>
                  </div>
                  <button
                    type="button"
                    @click="removeFile(rIdx)"
                    class="text-xs text-rose-500 hover:text-rose-700 font-bold hover:underline"
                  >
                    Ganti
                  </button>
                </div>
              </div>
            </template>

            <p v-if="form.errors.attachments" class="text-rose-500 text-xs font-medium">
              {{ form.errors.attachments }}
            </p>
          </div>
        </div>

        <!-- Security / Privacy Notice -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 flex items-start gap-3">
          <svg class="w-5 h-5 text-teal-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <div class="space-y-1">
            <span class="font-bold text-slate-700 block">Jaminan Privasi & Perlindungan Berkas Kependudukan</span>
            <p class="leading-relaxed">
              Semua berkas persyaratan dan data yang Anda unggah disimpan secara terenkripsi pada arsip privat server Desa Gelam Jaya. Dokumen hanya dapat diakses oleh staf pelayanan yang bertugas dan tidak dipublikasikan ke pihak luar.
            </p>
          </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <Link
            href="/warga/pengajuan"
            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-[0.98] disabled:opacity-50 flex items-center gap-2"
          >
            <span v-if="form.processing">Mengunggah & Menyimpan...</span>
            <span v-else>Kirim Permohonan Surat</span>
          </button>
        </div>
      </form>
    </div>
  </WargaLayout>
</template>
