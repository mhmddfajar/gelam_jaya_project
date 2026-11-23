<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  letterType: {
    type: Object,
    required: true,
  },
});

// Normalize form_fields options if stored as array
const initialFormFields = (props.letterType.form_fields || []).map(f => ({
  ...f,
  options: Array.isArray(f.options) ? f.options.join(', ') : (f.options || ''),
}));

const form = useForm({
  code: props.letterType.code || '',
  name: props.letterType.name || '',
  description: props.letterType.description || '',
  is_active: props.letterType.is_active ?? true,
  requirements: Array.isArray(props.letterType.requirements)
    ? [...props.letterType.requirements]
    : [],
  form_fields: initialFormFields,
});

// Quick suggestion chips for requirements
const quickSuggestions = [
  'KTP Pemohon',
  'Kartu Keluarga (KK)',
  'Surat Pengantar RT/RW',
  'Foto Tempat Usaha',
  'Akta Kelahiran / Ijazah',
  'Surat Keterangan RS/RT',
  'Buku Nikah',
];

const addRequirement = (text = '') => {
  form.requirements.push(text);
};

const removeRequirement = (index) => {
  form.requirements.splice(index, 1);
};

const addQuickSuggestion = (text) => {
  if (!form.requirements.includes(text)) {
    form.requirements.push(text);
  }
};

// Form builder actions
const addFormField = () => {
  form.form_fields.push({
    label: '',
    name: '',
    type: 'text',
    required: true,
    options: '',
  });
};

const removeFormField = (index) => {
  form.form_fields.splice(index, 1);
};

const handleLabelChange = (field) => {
  if (!field.name && field.label) {
    field.name = field.label
      .toLowerCase()
      .replace(/[^a-z0-9_]/g, '_')
      .replace(/_+/g, '_')
      .replace(/^_|_$/g, '');
  }
};

const submit = () => {
  form.put(`/admin/jenis-surat/${props.letterType.id}`);
};
</script>

<template>
  <Head :title="`Edit ${letterType.code} - ${letterType.name}`" />

  <AdminLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Back Link & Header -->
      <div class="flex items-center justify-between">
        <div class="space-y-1">
          <Link
            href="/admin/jenis-surat"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 transition"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Master Jenis Surat</span>
          </Link>
          <div class="flex items-center gap-3">
            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200">
              {{ letterType.code }}
            </span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
              Edit Jenis Surat
            </h1>
          </div>
          <p class="text-xs sm:text-sm text-slate-500">
            Perbarui nama surat, persyaratan berkas pendukung, dan skema kolom formulir isian tambahan.
          </p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Section 1: Informasi Utama -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">1. Informasi Pokok Surat</h2>
            <p class="text-xs text-slate-400 mt-0.5">Identitas surat dinas yang akan ditampilkan pada sistem.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Kode Surat -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Kode Surat <span class="text-rose-500">*</span>
              </label>
              <input
                type="text"
                v-model="form.code"
                placeholder="misal: SKU, SKD, SKTM"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono uppercase font-bold focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.code }"
              />
              <p v-if="form.errors.code" class="text-rose-500 text-xs mt-1 font-medium">
                {{ form.errors.code }}
              </p>
            </div>

            <!-- Nama Jenis Surat -->
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Nama Lengkap Surat <span class="text-rose-500">*</span>
              </label>
              <input
                type="text"
                v-model="form.name"
                placeholder="misal: Surat Keterangan Usaha"
                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition font-medium"
                :class="{ 'border-rose-300 bg-rose-50/30': form.errors.name }"
              />
              <p v-if="form.errors.name" class="text-rose-500 text-xs mt-1 font-medium">
                {{ form.errors.name }}
              </p>
            </div>
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              Deskripsi / Kegunaan Surat
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Jelaskan fungsi surat ini secara ringkas agar warga mudah memahami..."
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
              :class="{ 'border-rose-300 bg-rose-50/30': form.errors.description }"
            ></textarea>
            <p v-if="form.errors.description" class="text-rose-500 text-xs mt-1 font-medium">
              {{ form.errors.description }}
            </p>
          </div>

          <!-- Status Aktif Toggle -->
          <div class="flex items-center gap-3 pt-2">
            <input
              type="checkbox"
              id="is_active"
              v-model="form.is_active"
              class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500"
            />
            <label for="is_active" class="text-xs sm:text-sm font-semibold text-slate-700 cursor-pointer">
              Aktifkan jenis surat ini (Warga dan staf langsung dapat mengajukannya)
            </label>
          </div>
        </div>

        <!-- Section 2: Persyaratan Berkas Builder -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">2. Daftar Berkas Persyaratan</h2>
              <p class="text-xs text-slate-400 mt-0.5">Dokumen fisik / digital yang wajib diunggah pemohon saat mengajukan surat.</p>
            </div>

            <button
              type="button"
              @click="addRequirement('')"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>+ Tambah Syarat</span>
            </button>
          </div>

          <!-- Quick Suggestion Chips -->
          <div class="space-y-1.5">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Saran Cepat:</span>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="sug in quickSuggestions"
                :key="sug"
                type="button"
                @click="addQuickSuggestion(sug)"
                class="px-2.5 py-1 rounded-lg text-xs font-medium border transition"
                :class="form.requirements.includes(sug)
                  ? 'bg-teal-50 border-teal-200 text-teal-700'
                  : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'"
              >
                + {{ sug }}
              </button>
            </div>
          </div>

          <!-- Dynamic List of Requirements -->
          <div class="space-y-2.5">
            <div
              v-for="(req, index) in form.requirements"
              :key="index"
              class="flex items-center gap-2"
            >
              <span class="w-6 h-6 rounded-lg bg-teal-50 text-teal-700 font-bold text-xs flex items-center justify-center shrink-0">
                {{ index + 1 }}
              </span>
              <input
                type="text"
                v-model="form.requirements[index]"
                placeholder="Nama berkas persyaratan (contoh: KTP Pemohon)"
                class="flex-1 px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
              />
              <button
                type="button"
                @click="removeRequirement(index)"
                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition"
                title="Hapus syarat ini"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>

            <div
              v-if="form.requirements.length === 0"
              class="p-4 rounded-xl border border-dashed border-slate-200 text-center text-xs text-slate-400"
            >
              Belum ada berkas persyaratan yang ditentukan. Klik "+ Tambah Syarat" di atas.
            </div>
          </div>
        </div>

        <!-- Section 3: Skema Kolom Formulir Dinamis (Form Builder) -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">3. Kolom Formulir Isian Tambahan</h2>
              <p class="text-xs text-slate-400 mt-0.5">Input khusus yang harus diisi pemohon (misal: Nama Usaha, Tanggal Meninggal, Alamat Tujuan).</p>
            </div>

            <button
              type="button"
              @click="addFormField"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold rounded-xl border border-teal-200 transition"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>+ Tambah Kolom Input</span>
            </button>
          </div>

          <!-- Dynamic List of Form Fields -->
          <div class="space-y-4">
            <div
              v-for="(field, index) in form.form_fields"
              :key="index"
              class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3 relative group"
            >
              <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-teal-100 text-teal-800">
                  Kolom #{{ index + 1 }}
                </span>

                <button
                  type="button"
                  @click="removeFormField(index)"
                  class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:underline flex items-center gap-1"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  <span>Hapus Kolom</span>
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Label Input -->
                <div class="sm:col-span-2">
                  <label class="block text-[11px] font-bold text-slate-600 mb-1">
                    Label Kolom (Ditampilkan ke Pengguna)
                  </label>
                  <input
                    type="text"
                    v-model="field.label"
                    @blur="handleLabelChange(field)"
                    placeholder="misal: Nama Usaha"
                    class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition font-medium"
                  />
                </div>

                <!-- Tipe Input -->
                <div>
                  <label class="block text-[11px] font-bold text-slate-600 mb-1">
                    Tipe Input
                  </label>
                  <select
                    v-model="field.type"
                    class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                  >
                    <option value="text">Teks Singkat (text)</option>
                    <option value="textarea">Teks Panjang (textarea)</option>
                    <option value="number">Angka / Nominal (number)</option>
                    <option value="date">Tanggal (date)</option>
                    <option value="select">Pilihan Menu (select)</option>
                  </select>
                </div>
              </div>

              <!-- Options for Select -->
              <div v-if="field.type === 'select'" class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-600">
                  Opsi Pilihan (Pisahkan dengan koma)
                </label>
                <input
                  type="text"
                  v-model="field.options"
                  placeholder="misal: Perdagangan, Jasa, Pertanian, Kuliner"
                  class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                />
              </div>

              <!-- Wajib Diisi & Identifier -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-200/60 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="field.required"
                    class="w-3.5 h-3.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500"
                  />
                  <span class="font-bold text-slate-700">Wajib Diisi oleh Pemohon</span>
                </label>

                <div class="flex items-center gap-1.5 text-slate-400">
                  <span class="font-medium text-[11px]">Nama Variabel:</span>
                  <input
                    type="text"
                    v-model="field.name"
                    placeholder="nama_kolom"
                    class="px-2 py-1 text-xs font-mono bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-teal-500 w-36"
                  />
                </div>
              </div>
            </div>

            <div
              v-if="form.form_fields.length === 0"
              class="p-4 rounded-xl border border-dashed border-slate-200 text-center text-xs text-slate-400"
            >
              Belum ada kolom isian khusus. Surat hanya akan meminta field bawaan sistem (keperluan surat). Klik "+ Tambah Kolom Input" jika jenis surat ini membutuhkan data spesifik.
            </div>
          </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <Link
            href="/admin/jenis-surat"
            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-[0.98] disabled:opacity-50"
          >
            {{ form.processing ? 'Menyimpan...' : 'Perbarui Jenis Surat' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
