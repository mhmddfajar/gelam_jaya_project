<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => (user.value?.role === 'admin' ? AdminLayout : StafLayout));

const props = defineProps({
  residents: {
    type: Array,
    default: () => [],
  },
  letterTypes: {
    type: Array,
    default: () => [],
  },
  hamlets: {
    type: Array,
    default: () => [],
  },
  preselectedResidentId: {
    type: [String, Number],
    default: null,
  },
});

// Main Form for Offline Request
const form = useForm({
  resident_id: props.preselectedResidentId ? Number(props.preselectedResidentId) : '',
  letter_type_id: '',
  purpose: '',
  form_data: {},
  attachments: {},
});

// Search filter for resident dropdown
const residentSearch = ref('');
const filteredResidents = computed(() => {
  if (!residentSearch.value) {
    return props.residents.slice(0, 30);
  }
  const q = residentSearch.value.toLowerCase();
  return props.residents.filter(r =>
    r.name.toLowerCase().includes(q) ||
    r.nik.includes(q) ||
    (r.address && r.address.toLowerCase().includes(q))
  ).slice(0, 30);
});

const selectedResident = computed(() => {
  if (!form.resident_id) return null;
  return props.residents.find(r => r.id === Number(form.resident_id));
});

// Selected letter type & dynamic form fields
const selectedLetterType = computed(() => {
  if (!form.letter_type_id) return null;
  return props.letterTypes.find(t => t.id === Number(form.letter_type_id));
});

const formFields = computed(() => {
  if (!selectedLetterType.value) return [];
  return selectedLetterType.value.form_fields || selectedLetterType.value.form_schema || [];
});

// When letter type changes, reset and initialize dynamic form_data fields
watch(selectedLetterType, (newType) => {
  if (!newType) {
    form.form_data = {};
    form.attachments = {};
    return;
  }
  const initialData = {};
  const fields = newType.form_fields || newType.form_schema || [];
  fields.forEach(f => {
    const key = f.name || f.key;
    initialData[key] = '';
  });
  form.form_data = initialData;
  form.attachments = {};
});

const handleFileChange = (reqId, event) => {
  const file = event.target.files[0];
  if (file) {
    form.attachments[reqId] = file;
  } else {
    delete form.attachments[reqId];
  }
};

const submitRequest = () => {
  form.post('/staf/pengajuan', {
    forceFormData: true,
  });
};

// --- Modal Tambah Penduduk Baru ---
const showAddResidentModal = ref(false);
const newResidentForm = useForm({
  nik: '',
  family_card_no: '',
  name: '',
  birth_place: '',
  birth_date: '',
  gender: 'L',
  religion: 'Islam',
  marital_status: 'Belum Kawin',
  occupation: '',
  education: '',
  citizenship: 'WNI',
  address: '',
  hamlet_id: '',
  rt: '',
  rw: '',
  family_relation: '',
});

const openAddResidentModal = () => {
  newResidentForm.reset();
  newResidentForm.clearErrors();
  showAddResidentModal.value = true;
};

const closeAddResidentModal = () => {
  showAddResidentModal.value = false;
};

const submitNewResident = () => {
  newResidentForm.post('/staf/penduduk', {
    preserveScroll: true,
    onSuccess: (page) => {
      // Find newly added resident in the updated props by NIK
      const addedNik = newResidentForm.nik;
      closeAddResidentModal();
      setTimeout(() => {
        const found = props.residents.find(r => r.nik === addedNik);
        if (found) {
          form.resident_id = found.id;
        }
      }, 100);
    },
  });
};
</script>

<template>
  <Head title="Input Layanan Luring" />

  <component :is="currentLayout">
    <template #header>Input Layanan Pengajuan Luring</template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header & Breadcrumb -->
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
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
              Pendaftaran Surat Luring (Offline)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
              Input permohonan surat langsung bagi warga yang datang ke kantor desa Gelam Jaya. Status awal otomatis <strong>Diproses</strong>.
            </p>
          </div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold text-xs border border-emerald-200">
            🏢 Layanan Loket
          </span>
        </div>
      </div>

      <!-- Main Form Container -->
      <form @submit.prevent="submitRequest" class="space-y-6">
        <!-- Section 1: Pilih Data Penduduk -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900">
                1. Data Penduduk Pemohon
              </h2>
              <p class="text-[11px] text-slate-400">
                Pilih data warga yang mengajukan surat atau tambahkan baru jika belum terdata.
              </p>
            </div>

            <!-- Tombol Modal Tambah Penduduk Cepat -->
            <button
              type="button"
              @click="openAddResidentModal"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs border border-teal-200 transition shrink-0"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>+ Tambah Penduduk Baru</span>
            </button>
          </div>

          <!-- Select or Search Resident -->
          <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Cari Nama / NIK:
                </label>
                <input
                  v-model="residentSearch"
                  type="text"
                  placeholder="Ketik untuk menyaring nama / NIK..."
                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Pilih Penduduk <span class="text-rose-500">*</span>:
                </label>
                <select
                  v-model="form.resident_id"
                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition bg-white"
                  :class="{ 'border-rose-300 ring-1 ring-rose-200': form.errors.resident_id }"
                >
                  <option value="">-- Pilih Penduduk --</option>
                  <option
                    v-for="res in filteredResidents"
                    :key="res.id"
                    :value="res.id"
                  >
                    {{ res.name }} (NIK: {{ res.nik }})
                  </option>
                </select>
                <p v-if="form.errors.resident_id" class="text-xs text-rose-600 font-medium mt-1">
                  {{ form.errors.resident_id }}
                </p>
              </div>
            </div>

            <!-- Preview Data Penduduk Terpilih -->
            <div
              v-if="selectedResident"
              class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs"
            >
              <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Nama Pemohon:</span>
                <span class="font-bold text-slate-900 block mt-0.5">{{ selectedResident.name }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">NIK:</span>
                <span class="font-mono text-slate-800 block mt-0.5">{{ selectedResident.nik }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Alamat Domisili:</span>
                <span class="text-slate-700 block mt-0.5">
                  {{ selectedResident.address }} (RT {{ selectedResident.rt }}/RW {{ selectedResident.rw }})
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Pilih Jenis Surat -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
          <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
            2. Jenis Surat & Keperluan
          </h2>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Pilih Jenis Surat Dinas <span class="text-rose-500">*</span>:
              </label>
              <select
                v-model="form.letter_type_id"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition bg-white"
                :class="{ 'border-rose-300 ring-1 ring-rose-200': form.errors.letter_type_id }"
              >
                <option value="">-- Pilih Jenis Surat --</option>
                <option
                  v-for="t in letterTypes"
                  :key="t.id"
                  :value="t.id"
                >
                  [{{ t.code }}] {{ t.name }}
                </option>
              </select>
              <p v-if="form.errors.letter_type_id" class="text-xs text-rose-600 font-medium mt-1">
                {{ form.errors.letter_type_id }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Keperluan Pengajuan Surat <span class="text-rose-500">*</span>:
              </label>
              <textarea
                v-model="form.purpose"
                rows="2"
                placeholder="Contoh: Persyaratan melamar pekerjaan / pendaftaran kuliah..."
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                :class="{ 'border-rose-300 ring-1 ring-rose-200': form.errors.purpose }"
              ></textarea>
              <p v-if="form.errors.purpose" class="text-xs text-rose-600 font-medium mt-1">
                {{ form.errors.purpose }}
              </p>
            </div>
          </div>
        </div>

        <!-- Section 3: Isian Dinamis (form_schema) -->
        <div
          v-if="selectedLetterType && formFields.length > 0"
          class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 animate-fade-in"
        >
          <div class="border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold text-slate-900">
              3. Rincian Isian Khusus Formulir ({{ selectedLetterType.name }})
            </h2>
            <p class="text-[11px] text-slate-400">
              Isikan data tambahan sesuai formulir template surat yang dipilih.
            </p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
              v-for="field in formFields"
              :key="field.name || field.key"
              class="space-y-1"
            >
              <label class="block text-xs font-bold text-slate-700">
                {{ field.label || field.name || field.key }}
                <span v-if="field.required" class="text-rose-500">*</span>
              </label>

              <!-- Text Field -->
              <input
                v-if="field.type === 'text' || field.type === 'number' || field.type === 'date'"
                v-model="form.form_data[field.name || field.key]"
                :type="field.type"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                :class="{ 'border-rose-300': form.errors[`form_data.${field.name || field.key}`] }"
              />

              <!-- Textarea Field -->
              <textarea
                v-else-if="field.type === 'textarea'"
                v-model="form.form_data[field.name || field.key]"
                rows="2"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
                :class="{ 'border-rose-300': form.errors[`form_data.${field.name || field.key}`] }"
              ></textarea>

              <!-- Select Field -->
              <select
                v-else-if="field.type === 'select'"
                v-model="form.form_data[field.name || field.key]"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition bg-white"
                :class="{ 'border-rose-300': form.errors[`form_data.${field.name || field.key}`] }"
              >
                <option value="">-- Pilih Opsi --</option>
                <option
                  v-for="opt in (field.options || [])"
                  :key="opt"
                  :value="opt"
                >
                  {{ opt }}
                </option>
              </select>

              <p
                v-if="form.errors[`form_data.${field.name || field.key}`]"
                class="text-xs text-rose-600 font-medium mt-1"
              >
                {{ form.errors[`form_data.${field.name || field.key}`] }}
              </p>
            </div>
          </div>
        </div>

        <!-- Section 4: Berkas Persyaratan (Opsional Luring) -->
        <div
          v-if="selectedLetterType && selectedLetterType.type_requirements?.length > 0"
          class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 animate-fade-in"
        >
          <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-bold text-slate-900">
                4. Berkas Lampiran Dokumen
              </h2>
              <p class="text-[11px] text-slate-400">
                Unggah pindaian/foto dokumen jika warga membawa berkas fisik (bersifat opsional untuk layanan luring).
              </p>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
              Opsional Luring
            </span>
          </div>

          <div class="space-y-3">
            <div
              v-for="req in selectedLetterType.type_requirements"
              :key="req.id"
              class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs"
            >
              <div>
                <span class="font-bold text-slate-800 block">{{ req.name }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">
                  Format: JPG, PNG, atau PDF (maks. 2 MB)
                </span>
              </div>

              <div>
                <input
                  type="file"
                  accept=".jpg,.jpeg,.png,.pdf"
                  @change="handleFileChange(req.id, $event)"
                  class="text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Submit & Cancel Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <Link
            href="/staf/pengajuan"
            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm hover:shadow transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <span v-if="form.processing">Menyimpan Pengajuan...</span>
            <span v-else>Simpan Pengajuan Luring</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Quick Modal Tambah Penduduk Baru -->
    <div
      v-if="showAddResidentModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs overflow-y-auto"
    >
      <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl my-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-base font-bold text-slate-900">Tambah Penduduk Baru (Cepat)</h3>
            <p class="text-xs text-slate-400">
              Data penduduk yang diinput oleh staf akan otomatis terverifikasi (is_verified = true).
            </p>
          </div>
          <button
            type="button"
            @click="closeAddResidentModal"
            class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 text-lg leading-none"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="submitNewResident" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">NIK (16 Digit) <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.nik"
                type="text"
                maxlength="16"
                placeholder="16 digit angka..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                :class="{ 'border-rose-300': newResidentForm.errors.nik }"
              />
              <p v-if="newResidentForm.errors.nik" class="text-rose-600 mt-1">{{ newResidentForm.errors.nik }}</p>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">No. KK (Opsional)</label>
              <input
                v-model="newResidentForm.family_card_no"
                type="text"
                maxlength="16"
                placeholder="16 digit angka..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                :class="{ 'border-rose-300': newResidentForm.errors.family_card_no }"
              />
              <p v-if="newResidentForm.errors.family_card_no" class="text-rose-600 mt-1">{{ newResidentForm.errors.family_card_no }}</p>
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Nama Lengkap (KTP) <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.name"
                type="text"
                placeholder="Nama sesuai KTP..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                :class="{ 'border-rose-300': newResidentForm.errors.name }"
              />
              <p v-if="newResidentForm.errors.name" class="text-rose-600 mt-1">{{ newResidentForm.errors.name }}</p>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Tempat Lahir <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.birth_place"
                type="text"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.birth_date"
                type="date"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
              <select
                v-model="newResidentForm.gender"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 bg-white"
              >
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Agama <span class="text-rose-500">*</span></label>
              <select
                v-model="newResidentForm.religion"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 bg-white"
              >
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Status Perkawinan <span class="text-rose-500">*</span></label>
              <select
                v-model="newResidentForm.marital_status"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 bg-white"
              >
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Pekerjaan</label>
              <input
                v-model="newResidentForm.occupation"
                type="text"
                placeholder="Pekerjaan..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 mb-1">Alamat Domisili <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.address"
                type="text"
                placeholder="Nama jalan / nomor rumah..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">RT <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.rt"
                type="text"
                maxlength="3"
                placeholder="001"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">RW <span class="text-rose-500">*</span></label>
              <input
                v-model="newResidentForm.rw"
                type="text"
                maxlength="3"
                placeholder="001"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 mb-1">Dusun</label>
              <select
                v-model="newResidentForm.hamlet_id"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 bg-white"
              >
                <option value="">-- Pilih Dusun --</option>
                <option v-for="h in hamlets" :key="h.id" :value="h.id">{{ h.name }}</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button
              type="button"
              @click="closeAddResidentModal"
              class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="newResidentForm.processing"
              class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold disabled:opacity-50"
            >
              {{ newResidentForm.processing ? 'Menyimpan...' : 'Simpan Penduduk' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </component>
</template>
