<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
  residents: { type: Object, required: true },
  hamlets: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => user.value?.role === 'admin' ? AdminLayout : StafLayout);

// Search & Filter
const search = ref(props.filters.search || '');
const selectedHamlet = ref(props.filters.hamlet_id || '');
const selectedVerified = ref(props.filters.is_verified ?? '');

function applyFilter() {
  router.get('/staf/penduduk', {
    search: search.value,
    hamlet_id: selectedHamlet.value,
    is_verified: selectedVerified.value,
  }, { preserveState: true, replace: true });
}

function resetFilter() {
  search.value = '';
  selectedHamlet.value = '';
  selectedVerified.value = '';
  applyFilter();
}

// Modal state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedResident = ref(null);

const baseFormData = () => ({
  nik: '',
  family_card_no: '',
  name: '',
  birth_place: '',
  birth_date: '',
  gender: '',
  religion: '',
  marital_status: '',
  occupation: '',
  education: '',
  citizenship: 'WNI',
  address: '',
  hamlet_id: '',
  rt: '',
  rw: '',
  family_relation: '',
});

const createForm = useForm(baseFormData());
const editForm = useForm(baseFormData());

const religionOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const maritalOptions = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati', 'Duda', 'Janda'];

function openCreate() {
  createForm.reset();
  createForm.clearErrors();
  showCreateModal.value = true;
}

function submitCreate() {
  createForm.post('/staf/penduduk', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function openEdit(resident) {
  selectedResident.value = resident;
  Object.assign(editForm, {
    nik: resident.nik,
    family_card_no: resident.family_card_no || '',
    name: resident.name,
    birth_place: resident.birth_place,
    birth_date: resident.birth_date,
    gender: resident.gender,
    religion: resident.religion,
    marital_status: resident.marital_status,
    occupation: resident.occupation || '',
    education: resident.education || '',
    citizenship: resident.citizenship || 'WNI',
    address: resident.address,
    hamlet_id: resident.hamlet_id || '',
    rt: resident.rt,
    rw: resident.rw,
    family_relation: resident.family_relation || '',
  });
  editForm.clearErrors();
  showEditModal.value = true;
}

function submitEdit() {
  if (!selectedResident.value) return;
  editForm.put(`/staf/penduduk/${selectedResident.value.id}`, {
    onSuccess: () => { showEditModal.value = false; },
  });
}

// Confirmation dialogs
const confirmDialog = ref({ show: false, title: '', message: '', danger: false, action: null });

function confirmVerify(resident) {
  confirmDialog.value = {
    show: true,
    title: 'Verifikasi Data Penduduk',
    message: `Konfirmasi bahwa data kependudukan "${resident.name}" (NIK: ${resident.nik}) sudah sesuai dan benar?`,
    danger: false,
    confirmText: 'Ya, Verifikasi Data',
    action: () => {
      router.post(`/staf/penduduk/${resident.id}/verify`, {}, {
        onSuccess: () => { confirmDialog.value.show = false; },
      });
    },
  };
}

function confirmDelete(resident) {
  confirmDialog.value = {
    show: true,
    title: 'Hapus Data Penduduk',
    message: `Hapus data penduduk "${resident.name}"? Data tidak akan dihapus permanen (soft delete).`,
    danger: true,
    confirmText: 'Ya, Hapus Data',
    action: () => {
      router.delete(`/staf/penduduk/${resident.id}`, {
        onSuccess: () => { confirmDialog.value.show = false; },
      });
    },
  };
}
</script>

<template>
  <Head title="Data Penduduk" />

  <component :is="currentLayout">
    <template #header>Manajemen Data Penduduk</template>

    <div class="space-y-6">
      <!-- Filter Bar -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              🔍
            </span>
            <input
              type="text"
              v-model="search"
              @keyup.enter="applyFilter"
              placeholder="Cari nama, NIK, No. KK..."
              class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs transition"
            />
          </div>

          <select
            v-model="selectedHamlet"
            @change="applyFilter"
            class="px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 shadow-2xs bg-white text-slate-700 w-full sm:w-auto"
          >
            <option value="">Semua Dusun</option>
            <option v-for="h in hamlets" :key="h.id" :value="h.id">{{ h.name }}</option>
          </select>

          <select
            v-model="selectedVerified"
            @change="applyFilter"
            class="px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 shadow-2xs bg-white text-slate-700 w-full sm:w-auto"
          >
            <option value="">Semua Status Verifikasi</option>
            <option value="1">Terverifikasi</option>
            <option value="0">Belum Terverifikasi</option>
          </select>

          <button
            @click="applyFilter"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shadow-2xs"
          >
            Filter
          </button>
          <button
            v-if="search || selectedHamlet || selectedVerified !== ''"
            @click="resetFilter"
            class="text-xs text-slate-500 hover:text-slate-800 underline font-medium transition"
          >
            Reset
          </button>
        </div>

        <button
          @click="openCreate"
          class="shrink-0 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all flex items-center gap-2 justify-center w-full md:w-auto"
        >
          <span class="text-base leading-none">＋</span>
          <span>Tambah Penduduk</span>
        </button>
      </div>

      <!-- 4 Stat Cards Row (Modern SaaS Clean Soft Light) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Penduduk -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Penduduk</span>
            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
              👥
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">{{ residents.total }}</span>
          </div>
          <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
            <span>Terdaftar di master data desa</span>
          </p>
        </div>

        <!-- Card 2: Terverifikasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terverifikasi</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-base group-hover:scale-105 transition-transform">
              ✓
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">
              {{ residents.data.filter(r => r.is_verified).length }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Data resmi valid (di halaman ini)</span>
          </p>
        </div>

        <!-- Card 3: Belum Terverifikasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Belum Verifikasi</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
              ⚠️
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-black text-amber-500 tracking-tight">
              {{ residents.data.filter(r => !r.is_verified).length }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
            <span>Menunggu verifikasi staf</span>
          </p>
        </div>

        <!-- Card 4: Jumlah Dusun -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Wilayah Dusun</span>
            <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
              🏘️
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-black text-teal-600 tracking-tight">{{ hamlets.length }}</span>
          </div>
          <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
            <span>Dusun terdata di Gelam Jaya</span>
          </p>
        </div>
      </div>

      <!-- Data Table (Clean Table Style) -->
      <DataTable :pagination="residents" :is-empty="residents.data.length === 0" empty-text="Tidak ada data penduduk yang cocok dengan pencarian.">
        <template #header>
          <tr>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & NIK</th>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tempat & Tgl Lahir</th>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat & Dusun</th>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akun</th>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Verifikasi</th>
            <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </template>

        <tr v-for="r in residents.data" :key="r.id" class="hover:bg-teal-50/20 transition group">
          <td class="px-5 py-4">
            <p class="font-bold text-slate-800 text-sm group-hover:text-teal-700 transition">{{ r.name }}</p>
            <p class="text-xs font-mono font-medium text-slate-500 mt-0.5 tracking-tight">{{ r.nik }}</p>
            <span class="inline-block text-[11px] text-slate-400 mt-0.5 font-medium">
              {{ r.gender === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
            </span>
          </td>
          <td class="px-5 py-4 text-xs text-slate-700">
            <p class="font-semibold text-slate-800">{{ r.birth_place }}</p>
            <p class="text-slate-400 mt-0.5">{{ r.birth_date }}</p>
          </td>
          <td class="px-5 py-4 text-xs text-slate-700">
            <p class="font-medium text-slate-800">{{ r.address }}</p>
            <p class="text-slate-400 mt-0.5">RT {{ r.rt }} / RW {{ r.rw }}</p>
            <span v-if="r.hamlet" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-teal-50 text-teal-700 border border-teal-200/60 mt-1">
              📍 {{ r.hamlet.name }}
            </span>
          </td>
          <td class="px-5 py-4">
            <span v-if="r.user" class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
              Ada akun ({{ r.user.role }})
            </span>
            <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
              Belum punya akun
            </span>
          </td>
          <td class="px-5 py-4">
            <StatusBadge
              :status="r.is_verified ? 'active' : 'inactive'"
              :label="r.is_verified ? 'Terverifikasi' : 'Belum Verifikasi'"
            />
          </td>
          <td class="px-5 py-4 text-right">
            <div class="flex items-center justify-end gap-1.5 flex-wrap">
              <button
                @click="openEdit(r)"
                class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shadow-2xs"
              >
                Edit
              </button>
              <button
                v-if="!r.is_verified"
                @click="confirmVerify(r)"
                class="px-3 py-1.5 rounded-xl border border-emerald-200/80 text-xs font-semibold text-emerald-700 bg-emerald-50/60 hover:bg-emerald-100 transition shadow-2xs"
              >
                Verifikasi
              </button>
              <button
                v-if="!r.user"
                @click="confirmDelete(r)"
                class="px-3 py-1.5 rounded-xl border border-rose-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs"
              >
                Hapus
              </button>
            </div>
          </td>
        </tr>
      </DataTable>
    </div>

    <!-- Modal Tambah Penduduk -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-start justify-center p-4 pt-10">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Tambah Data Penduduk Baru</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800">
            <strong>Info:</strong> Penduduk yang ditambahkan oleh staf akan otomatis berstatus <strong>Terverifikasi</strong>.
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="NIK (16 digit)" id="c_nik" :error="createForm.errors.nik" required>
              <input id="c_nik" type="text" maxlength="16" v-model="createForm.nik" placeholder="16 digit NIK KTP"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs font-mono"
                :class="{ 'border-rose-300': createForm.errors.nik }" />
            </FormField>
            <FormField label="No. Kartu Keluarga" id="c_kk" :error="createForm.errors.family_card_no">
              <input id="c_kk" type="text" maxlength="16" v-model="createForm.family_card_no" placeholder="16 digit No KK (opsional)"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs font-mono" />
            </FormField>
          </div>

          <FormField label="Nama Lengkap" id="c_name" :error="createForm.errors.name" required>
            <input id="c_name" type="text" v-model="createForm.name" placeholder="Sesuai KTP"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
              :class="{ 'border-rose-300': createForm.errors.name }" />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Tempat Lahir" id="c_bplace" :error="createForm.errors.birth_place" required>
              <input id="c_bplace" type="text" v-model="createForm.birth_place"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Tanggal Lahir" id="c_bdate" :error="createForm.errors.birth_date" required>
              <input id="c_bdate" type="date" v-model="createForm.birth_date"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField label="Jenis Kelamin" id="c_gender" :error="createForm.errors.gender" required>
              <select id="c_gender" v-model="createForm.gender"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="" disabled>Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </FormField>
            <FormField label="Agama" id="c_religion" :error="createForm.errors.religion" required>
              <select id="c_religion" v-model="createForm.religion"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="" disabled>Pilih</option>
                <option v-for="rel in religionOptions" :key="rel" :value="rel">{{ rel }}</option>
              </select>
            </FormField>
            <FormField label="Status Perkawinan" id="c_marital" :error="createForm.errors.marital_status" required>
              <select id="c_marital" v-model="createForm.marital_status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="" disabled>Pilih</option>
                <option v-for="m in maritalOptions" :key="m" :value="m">{{ m }}</option>
              </select>
            </FormField>
          </div>

          <FormField label="Alamat Lengkap" id="c_address" :error="createForm.errors.address" required>
            <textarea id="c_address" rows="2" v-model="createForm.address"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"></textarea>
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField label="Dusun" id="c_hamlet">
              <select id="c_hamlet" v-model="createForm.hamlet_id"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="">Pilih Dusun (opsional)</option>
                <option v-for="h in hamlets" :key="h.id" :value="h.id">{{ h.name }}</option>
              </select>
            </FormField>
            <FormField label="RT" id="c_rt" :error="createForm.errors.rt" required>
              <input id="c_rt" type="text" maxlength="3" v-model="createForm.rt"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="RW" id="c_rw" :error="createForm.errors.rw" required>
              <input id="c_rw" type="text" maxlength="3" v-model="createForm.rw"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Pekerjaan" id="c_occ">
              <input id="c_occ" type="text" v-model="createForm.occupation" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Pendidikan Terakhir" id="c_edu">
              <input id="c_edu" type="text" v-model="createForm.education" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Hubungan Keluarga" id="c_rel">
              <input id="c_rel" type="text" v-model="createForm.family_relation" placeholder="Contoh: Kepala Keluarga, Istri"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Kewarganegaraan" id="c_citizen">
              <input id="c_citizen" type="text" v-model="createForm.citizenship"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
              Batal
            </button>
            <button type="submit" :disabled="createForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition-all disabled:opacity-50">
              <span v-if="createForm.processing">Menyimpan...</span>
              <span v-else>Simpan Penduduk</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Penduduk -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-start justify-center p-4 pt-10">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <div>
            <h3 class="text-lg font-bold text-slate-900">Ubah Data Penduduk</h3>
            <p class="text-xs text-slate-500 mt-0.5">NIK: <span class="font-mono font-bold">{{ selectedResident?.nik }}</span> (tidak dapat diubah)</p>
          </div>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <!-- NIK read-only display -->
          <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
            <strong>Perhatian:</strong> NIK tidak bisa diubah. Untuk perubahan NIK, hubungi administrator sistem.
          </div>

          <FormField label="Nama Lengkap" id="e_name" :error="editForm.errors.name" required>
            <input id="e_name" type="text" v-model="editForm.name"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
              :class="{ 'border-rose-300': editForm.errors.name }" />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="No. KK" id="e_kk" :error="editForm.errors.family_card_no">
              <input id="e_kk" type="text" maxlength="16" v-model="editForm.family_card_no" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs font-mono" />
            </FormField>
            <FormField label="Tanggal Lahir" id="e_bdate" :error="editForm.errors.birth_date" required>
              <input id="e_bdate" type="date" v-model="editForm.birth_date"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Tempat Lahir" id="e_bplace" :error="editForm.errors.birth_place" required>
              <input id="e_bplace" type="text" v-model="editForm.birth_place"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Jenis Kelamin" id="e_gender" :error="editForm.errors.gender" required>
              <select id="e_gender" v-model="editForm.gender"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Agama" id="e_religion" :error="editForm.errors.religion" required>
              <select id="e_religion" v-model="editForm.religion"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option v-for="rel in religionOptions" :key="rel" :value="rel">{{ rel }}</option>
              </select>
            </FormField>
            <FormField label="Status Perkawinan" id="e_marital" :error="editForm.errors.marital_status" required>
              <select id="e_marital" v-model="editForm.marital_status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option v-for="m in maritalOptions" :key="m" :value="m">{{ m }}</option>
              </select>
            </FormField>
          </div>

          <FormField label="Alamat Lengkap" id="e_address" :error="editForm.errors.address" required>
            <textarea id="e_address" rows="2" v-model="editForm.address"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"></textarea>
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField label="Dusun" id="e_hamlet">
              <select id="e_hamlet" v-model="editForm.hamlet_id"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white">
                <option value="">Tidak ada / belum diketahui</option>
                <option v-for="h in hamlets" :key="h.id" :value="h.id">{{ h.name }}</option>
              </select>
            </FormField>
            <FormField label="RT" id="e_rt" :error="editForm.errors.rt" required>
              <input id="e_rt" type="text" maxlength="3" v-model="editForm.rt"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="RW" id="e_rw" :error="editForm.errors.rw" required>
              <input id="e_rw" type="text" maxlength="3" v-model="editForm.rw"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Pekerjaan" id="e_occ">
              <input id="e_occ" type="text" v-model="editForm.occupation" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Pendidikan Terakhir" id="e_edu">
              <input id="e_edu" type="text" v-model="editForm.education" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Hubungan Keluarga" id="e_rel">
              <input id="e_rel" type="text" v-model="editForm.family_relation" placeholder="Opsional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
            <FormField label="Kewarganegaraan" id="e_citizen">
              <input id="e_citizen" type="text" v-model="editForm.citizenship"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs" />
            </FormField>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button type="button" @click="showEditModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
              Batal
            </button>
            <button type="submit" :disabled="editForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition-all disabled:opacity-50">
              <span v-if="editForm.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <ConfirmDialog
      :show="confirmDialog.show"
      :title="confirmDialog.title"
      :message="confirmDialog.message"
      :confirm-text="confirmDialog.confirmText"
      :danger="confirmDialog.danger"
      @confirm="confirmDialog.action"
      @close="confirmDialog.show = false"
    />
  </component>
</template>

