<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import FormField from '@/Components/FormField.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
  potentials: {
    type: Object,
    required: true,
  },
  categories: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({ search: '', category: '' }),
  },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => user.value?.role === 'admin' ? AdminLayout : StafLayout);

// Filter State
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

function applyFilter() {
  router.get('/staf/desa/potensi', {
    search: search.value,
    category: selectedCategory.value,
  }, { preserveState: true, replace: true });
}

function resetFilter() {
  search.value = '';
  selectedCategory.value = '';
  applyFilter();
}

// Modal State & Forms
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedItem = ref(null);

const createForm = useForm({
  title: '',
  category: 'umkm',
  location: '',
  description: '',
  photo: null,
});

const editForm = useForm({
  _method: 'PUT',
  title: '',
  category: '',
  location: '',
  description: '',
  photo: null,
});

function openCreate() {
  createForm.reset();
  createForm.category = 'umkm';
  createForm.clearErrors();
  showCreateModal.value = true;
}

function submitCreate() {
  createForm.post('/staf/desa/potensi', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function openEdit(item) {
  selectedItem.value = item;
  editForm.title = item.title;
  editForm.category = item.category;
  editForm.location = item.location || '';
  editForm.description = item.description;
  editForm.photo = null;
  editForm.clearErrors();
  showEditModal.value = true;
}

function submitEdit() {
  if (!selectedItem.value) return;
  editForm.post(`/staf/desa/potensi/${selectedItem.value.id}`, {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
}

// Confirm Delete Dialog
const confirmDialog = ref({
  show: false,
  title: '',
  message: '',
  confirmText: '',
  danger: true,
  action: null,
});

function confirmDelete(item) {
  confirmDialog.value = {
    show: true,
    title: 'Hapus Potensi Desa',
    message: `Apakah Anda yakin ingin menghapus data potensi "${item.title}"?`,
    confirmText: 'Ya, Hapus Data',
    danger: true,
    action: () => {
      router.delete(`/staf/desa/potensi/${item.id}`, {
        onSuccess: () => {
          confirmDialog.value.show = false;
        },
      });
    },
  };
}
</script>

<template>
  <Head title="Kelola Potensi Desa" />

  <component :is="currentLayout">
    <template #header>Kelola Potensi Desa</template>

    <div class="space-y-6">
      <!-- Top Action & Filter Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              🔍
            </span>
            <input
              type="text"
              v-model="search"
              @keyup.enter="applyFilter"
              placeholder="Cari potensi atau lokasi..."
              class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs transition"
            />
          </div>

          <select
            v-model="selectedCategory"
            @change="applyFilter"
            class="px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 shadow-2xs bg-white text-slate-700 w-full sm:w-auto"
          >
            <option value="">Semua Kategori</option>
            <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
          </select>

          <button
            @click="applyFilter"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shadow-2xs"
          >
            Filter
          </button>
          <button
            v-if="search || selectedCategory"
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
          <span>Tambah Potensi Desa</span>
        </button>
      </div>

      <!-- Data Table -->
      <DataTable :pagination="potentials" :is-empty="potentials.data.length === 0" empty-text="Tidak ada data potensi yang sesuai kriteria.">
        <template #header>
          <tr>
            <th class="px-5 py-3.5">Potensi & Lokasi</th>
            <th class="px-5 py-3.5">Kategori</th>
            <th class="px-5 py-3.5">Ringkasan Deskripsi</th>
            <th class="px-5 py-3.5 text-right">Aksi</th>
          </tr>
        </template>

        <tr v-for="item in potentials.data" :key="item.id" class="hover:bg-teal-50/20 transition group">
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-lg shadow-2xs">
                <img v-if="item.photo_path" :src="item.photo_path" :alt="item.title" class="w-full h-full object-cover" />
                <span v-else>🌾</span>
              </div>
              <div>
                <p class="font-bold text-slate-800 text-sm group-hover:text-teal-700 transition">{{ item.title }}</p>
                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                  <span>📍</span>
                  <span>{{ item.location || 'Wilayah Desa' }}</span>
                </p>
              </div>
            </div>
          </td>

          <td class="px-5 py-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200/80">
              {{ categories[item.category] || item.category }}
            </span>
          </td>

          <td class="px-5 py-4 text-xs text-slate-600 max-w-md">
            <p class="line-clamp-2 leading-relaxed">{{ item.description }}</p>
          </td>

          <td class="px-5 py-4 text-right">
            <div class="flex items-center justify-end gap-1.5">
              <button
                @click="openEdit(item)"
                class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shadow-2xs"
              >
                Edit
              </button>
              <button
                @click="confirmDelete(item)"
                class="px-3 py-1.5 rounded-xl border border-rose-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs"
              >
                Hapus
              </button>
            </div>
          </td>
        </tr>
      </DataTable>
    </div>

    <!-- Modal Tambah Potensi -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Tambah Potensi Desa</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <FormField label="Nama / Judul Potensi" id="pc_title" :error="createForm.errors.title" required>
            <input
              id="pc_title"
              type="text"
              v-model="createForm.title"
              placeholder="Contoh: Kawasan Budidaya Ikan Nila Bioflok"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Kategori Potensi" id="pc_cat" :error="createForm.errors.category" required>
              <select
                id="pc_cat"
                v-model="createForm.category"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>

            <FormField label="Lokasi / Dusun" id="pc_loc" :error="createForm.errors.location">
              <input
                id="pc_loc"
                type="text"
                v-model="createForm.location"
                placeholder="Contoh: Dusun Mekarjaya, RT 02"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>
          </div>

          <FormField label="Deskripsi Potensi & Keunggulan" id="pc_desc" :error="createForm.errors.description" required>
            <textarea
              id="pc_desc"
              rows="4"
              v-model="createForm.description"
              placeholder="Jelaskan mengenai potensi desa, kapasitas produksi, atau keunikannya..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
          </FormField>

          <FormField label="Foto Dokumentasi (Opsional, Maks 2MB)" id="pc_photo" :error="createForm.errors.photo">
            <input
              id="pc_photo"
              type="file"
              accept="image/*"
              @change="createForm.photo = $event.target.files[0]"
              class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
            />
          </FormField>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="createForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="createForm.processing">Menyimpan...</span>
              <span v-else>Simpan Potensi</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Potensi -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Ubah Data Potensi Desa</h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <FormField label="Nama / Judul Potensi" id="pe_title" :error="editForm.errors.title" required>
            <input
              id="pe_title"
              type="text"
              v-model="editForm.title"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Kategori Potensi" id="pe_cat" :error="editForm.errors.category" required>
              <select
                id="pe_cat"
                v-model="editForm.category"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>

            <FormField label="Lokasi / Dusun" id="pe_loc" :error="editForm.errors.location">
              <input
                id="pe_loc"
                type="text"
                v-model="editForm.location"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>
          </div>

          <FormField label="Deskripsi Potensi & Keunggulan" id="pe_desc" :error="editForm.errors.description" required>
            <textarea
              id="pe_desc"
              rows="4"
              v-model="editForm.description"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
          </FormField>

          <FormField label="Ganti Foto Dokumentasi (Opsional)" id="pe_photo" :error="editForm.errors.photo">
            <input
              id="pe_photo"
              type="file"
              accept="image/*"
              @change="editForm.photo = $event.target.files[0]"
              class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
            />
          </FormField>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showEditModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="editForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="editForm.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Dialog -->
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

