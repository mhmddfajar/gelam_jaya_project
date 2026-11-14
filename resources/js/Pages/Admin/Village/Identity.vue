<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';
import DataTable from '@/Components/DataTable.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
  hamlets: {
    type: Array,
    default: () => [],
  },
});

// Profile Form
const profileForm = useForm({
  _method: 'PUT',
  name: props.profile.name || '',
  village_code: props.profile.village_code || '',
  address: props.profile.address || '',
  phone: props.profile.phone || '',
  email: props.profile.email || '',
  district: props.profile.district || '',
  regency: props.profile.regency || '',
  province: props.profile.province || '',
  postal_code: props.profile.postal_code || '',
  area_size_ha: props.profile.area_size_ha || '',
  total_population: props.profile.total_population || '',
  total_households: props.profile.total_households || '',
  male_population: props.profile.male_population || '',
  female_population: props.profile.female_population || '',
  boundary_north: props.profile.boundary_north || '',
  boundary_south: props.profile.boundary_south || '',
  boundary_east: props.profile.boundary_east || '',
  boundary_west: props.profile.boundary_west || '',
  logo: null,
});

function submitProfile() {
  profileForm.post('/admin/desa/identitas-wilayah', {
    preserveScroll: true,
  });
}

// Hamlet Modals & Forms
const showCreateHamletModal = ref(false);
const showEditHamletModal = ref(false);
const selectedHamlet = ref(null);

const createHamletForm = useForm({
  name: '',
  head_name: '',
});

const editHamletForm = useForm({
  name: '',
  head_name: '',
});

function openCreateHamlet() {
  createHamletForm.reset();
  createHamletForm.clearErrors();
  showCreateHamletModal.value = true;
}

function submitCreateHamlet() {
  createHamletForm.post('/admin/desa/dusun', {
    onSuccess: () => {
      showCreateHamletModal.value = false;
      createHamletForm.reset();
    },
  });
}

function openEditHamlet(hamlet) {
  selectedHamlet.value = hamlet;
  editHamletForm.name = hamlet.name;
  editHamletForm.head_name = hamlet.head_name || '';
  editHamletForm.clearErrors();
  showEditHamletModal.value = true;
}

function submitEditHamlet() {
  if (!selectedHamlet.value) return;
  editHamletForm.put(`/admin/desa/dusun/${selectedHamlet.value.id}`, {
    onSuccess: () => {
      showEditHamletModal.value = false;
    },
  });
}

// Confirm Delete Hamlet
const confirmDialog = ref({
  show: false,
  title: '',
  message: '',
  confirmText: '',
  danger: true,
  action: null,
});

function confirmDeleteHamlet(hamlet) {
  if (hamlet.residents_count > 0) {
    alert(`Dusun "${hamlet.name}" tidak dapat dihapus karena masih memiliki ${hamlet.residents_count} penduduk.`);
    return;
  }

  confirmDialog.value = {
    show: true,
    title: 'Hapus Dusun',
    message: `Apakah Anda yakin ingin menghapus dusun "${hamlet.name}"?`,
    confirmText: 'Ya, Hapus Dusun',
    danger: true,
    action: () => {
      router.delete(`/admin/desa/dusun/${hamlet.id}`, {
        onSuccess: () => {
          confirmDialog.value.show = false;
        },
      });
    },
  };
}
</script>

<template>
  <Head title="Identitas & Wilayah Desa" />

  <AdminLayout>
    <template #header>Identitas & Wilayah Desa</template>

    <div class="space-y-8">
      <!-- Section 1: Form Profil Identitas Desa -->
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
              <span>🏛️</span>
              <span>Identitas Pokok & Batas Wilayah Desa</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
              Informasi profil resmi pemerintah Desa Gelam Jaya yang ditampilkan kepada publik dan kop surat.
            </p>
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-xl bg-teal-50 text-teal-700 border border-teal-200/80 text-xs font-semibold self-start sm:self-auto">
            1 Baris Data Desa
          </span>
        </div>

        <form @submit.prevent="submitProfile" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <FormField label="Nama Desa" id="p_name" :error="profileForm.errors.name" required>
              <input
                id="p_name"
                type="text"
                v-model="profileForm.name"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>

            <FormField label="Kode Desa (Kemendagri)" id="p_code" :error="profileForm.errors.village_code">
              <input
                id="p_code"
                type="text"
                v-model="profileForm.village_code"
                placeholder="Contoh: 36.03.11.2005"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Luas Wilayah (Hektar / Ha)" id="p_area" :error="profileForm.errors.area_size_ha">
              <input
                id="p_area"
                type="number"
                step="0.01"
                v-model="profileForm.area_size_ha"
                placeholder="Contoh: 450.50"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Kecamatan" id="p_district" :error="profileForm.errors.district" required>
              <input
                id="p_district"
                type="text"
                v-model="profileForm.district"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>

            <FormField label="Kabupaten / Kota" id="p_regency" :error="profileForm.errors.regency" required>
              <input
                id="p_regency"
                type="text"
                v-model="profileForm.regency"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>

            <FormField label="Provinsi" id="p_province" :error="profileForm.errors.province" required>
              <input
                id="p_province"
                type="text"
                v-model="profileForm.province"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>

            <FormField label="Kode Pos" id="p_postal" :error="profileForm.errors.postal_code">
              <input
                id="p_postal"
                type="text"
                v-model="profileForm.postal_code"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Nomor Telepon Kantor" id="p_phone" :error="profileForm.errors.phone">
              <input
                id="p_phone"
                type="text"
                v-model="profileForm.phone"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>

            <FormField label="Email Resmi Desa" id="p_email" :error="profileForm.errors.email">
              <input
                id="p_email"
                type="email"
                v-model="profileForm.email"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>
          </div>

          <FormField label="Alamat Kantor Desa Lengkap" id="p_addr" :error="profileForm.errors.address">
            <textarea
              id="p_addr"
              rows="2"
              v-model="profileForm.address"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
          </FormField>

          <!-- Statistik Demografi Penduduk Riil -->
          <div class="pt-4 border-t border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Statistik Demografi & Kependudukan Riil</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <FormField label="Total Penduduk (Jiwa)" id="p_tot_pop" :error="profileForm.errors.total_population">
                <input
                  id="p_tot_pop"
                  type="number"
                  min="0"
                  v-model="profileForm.total_population"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
                  placeholder="40030"
                />
              </FormField>

              <FormField label="Total Kepala Keluarga (KK)" id="p_tot_kk" :error="profileForm.errors.total_households">
                <input
                  id="p_tot_kk"
                  type="number"
                  min="0"
                  v-model="profileForm.total_households"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
                  placeholder="11015"
                />
              </FormField>

              <FormField label="Penduduk Laki-Laki (Jiwa)" id="p_male_pop" :error="profileForm.errors.male_population">
                <input
                  id="p_male_pop"
                  type="number"
                  min="0"
                  v-model="profileForm.male_population"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
                  placeholder="20218"
                />
              </FormField>

              <FormField label="Penduduk Perempuan (Jiwa)" id="p_fem_pop" :error="profileForm.errors.female_population">
                <input
                  id="p_fem_pop"
                  type="number"
                  min="0"
                  v-model="profileForm.female_population"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
                  placeholder="19812"
                />
              </FormField>
            </div>
          </div>

          <!-- Batas Wilayah -->
          <div class="pt-4 border-t border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Batas-Batas Wilayah Desa</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <FormField label="Sebelah Utara" id="b_north" :error="profileForm.errors.boundary_north">
                <input
                  id="b_north"
                  type="text"
                  v-model="profileForm.boundary_north"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
                />
              </FormField>

              <FormField label="Sebelah Selatan" id="b_south" :error="profileForm.errors.boundary_south">
                <input
                  id="b_south"
                  type="text"
                  v-model="profileForm.boundary_south"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
                />
              </FormField>

              <FormField label="Sebelah Timur" id="b_east" :error="profileForm.errors.boundary_east">
                <input
                  id="b_east"
                  type="text"
                  v-model="profileForm.boundary_east"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
                />
              </FormField>

              <FormField label="Sebelah Barat" id="b_west" :error="profileForm.errors.boundary_west">
                <input
                  id="b_west"
                  type="text"
                  v-model="profileForm.boundary_west"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
                />
              </FormField>
            </div>
          </div>

          <!-- Logo Upload -->
          <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div v-if="profile.logo_path" class="w-16 h-16 rounded-xl border border-slate-200 p-1 flex items-center justify-center bg-slate-50 shrink-0">
              <img :src="profile.logo_path" alt="Logo Desa" class="max-h-full max-w-full object-contain" />
            </div>
            <div class="flex-1">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Logo Resmi Desa (PNG / JPG / WEBP, Maks. 2MB)
              </label>
              <input
                type="file"
                accept="image/*"
                @change="profileForm.logo = $event.target.files[0]"
                class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
              />
              <p v-if="profileForm.errors.logo" class="text-xs text-rose-600 mt-1">{{ profileForm.errors.logo }}</p>
            </div>
          </div>

          <!-- Save Button -->
          <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
              type="submit"
              :disabled="profileForm.processing"
              class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <span v-if="profileForm.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan Identitas Desa</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Section 2: CRUD Wilayah Dusun (hamlets) -->
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
              <span>🗺️</span>
              <span>Daftar Wilayah Dusun di Desa</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
              Pembagian wilayah dusun yang digunakan untuk pengelompokan data kependudukan dan statistik desa.
            </p>
          </div>

          <button
            @click="openCreateHamlet"
            class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all flex items-center gap-2 justify-center shrink-0"
          >
            <span class="text-base leading-none">＋</span>
            <span>Tambah Dusun</span>
          </button>
        </div>

        <!-- Hamlets Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
          <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50/90 text-slate-500 font-bold uppercase text-[11px] tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5">Nama Dusun</th>
                <th class="px-5 py-3.5">Kepala Dusun</th>
                <th class="px-5 py-3.5 text-center">Jumlah Penduduk</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800">
              <tr v-for="h in hamlets" :key="h.id" class="hover:bg-teal-50/20 transition group">
                <td class="px-5 py-4 font-bold text-slate-800 group-hover:text-teal-700 transition">
                  📍 {{ h.name }}
                </td>
                <td class="px-5 py-4 text-xs text-slate-600">
                  <span v-if="h.head_name" class="font-medium text-slate-800">{{ h.head_name }}</span>
                  <span v-else class="text-slate-400 italic">Belum ditentukan</span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                    {{ h.residents_count }} warga
                  </span>
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      @click="openEditHamlet(h)"
                      class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shadow-2xs"
                    >
                      Edit
                    </button>
                    <button
                      @click="confirmDeleteHamlet(h)"
                      class="px-3 py-1.5 rounded-xl border border-rose-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed"
                      :disabled="h.residents_count > 0"
                      :title="h.residents_count > 0 ? 'Dusun tidak bisa dihapus karena masih ada penduduk' : 'Hapus Dusun'"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="hamlets.length === 0">
                <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs">
                  Belum ada data dusun. Tekan tombol Tambah Dusun untuk memulai.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Dusun -->
    <div v-if="showCreateHamletModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateHamletModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="text-lg font-bold text-slate-900">Tambah Dusun Baru</h3>
          <button @click="showCreateHamletModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitCreateHamlet" class="space-y-4">
          <FormField label="Nama Dusun" id="hc_name" :error="createHamletForm.errors.name" required>
            <input
              id="hc_name"
              type="text"
              v-model="createHamletForm.name"
              placeholder="Contoh: Dusun Sukamaju"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <FormField label="Nama Kepala Dusun (Opsional)" id="hc_head" :error="createHamletForm.errors.head_name">
            <input
              id="hc_head"
              type="text"
              v-model="createHamletForm.head_name"
              placeholder="Contoh: Bpk. Ahmad Dahlan"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showCreateHamletModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="createHamletForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="createHamletForm.processing">Menyimpan...</span>
              <span v-else>Simpan Dusun</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Dusun -->
    <div v-if="showEditHamletModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditHamletModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="text-lg font-bold text-slate-900">Ubah Data Dusun</h3>
          <button @click="showEditHamletModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitEditHamlet" class="space-y-4">
          <FormField label="Nama Dusun" id="he_name" :error="editHamletForm.errors.name" required>
            <input
              id="he_name"
              type="text"
              v-model="editHamletForm.name"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <FormField label="Nama Kepala Dusun" id="he_head" :error="editHamletForm.errors.head_name">
            <input
              id="he_head"
              type="text"
              v-model="editHamletForm.head_name"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showEditHamletModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="editHamletForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="editHamletForm.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Dialog Component -->
    <ConfirmDialog
      :show="confirmDialog.show"
      :title="confirmDialog.title"
      :message="confirmDialog.message"
      :confirm-text="confirmDialog.confirmText"
      :danger="confirmDialog.danger"
      @confirm="confirmDialog.action"
      @close="confirmDialog.show = false"
    />
  </AdminLayout>
</template>

