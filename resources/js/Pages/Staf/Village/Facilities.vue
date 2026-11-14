<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import FormField from '@/Components/FormField.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
  facilities: {
    type: Object,
    required: true,
  },
  categories: {
    type: Object,
    default: () => ({}),
  },
  conditions: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({ search: '', category: '', condition: '' }),
  },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => user.value?.role === 'admin' ? AdminLayout : StafLayout);

// Filter State
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedCondition = ref(props.filters.condition || '');

function applyFilter() {
  router.get('/staf/desa/sarana', {
    search: search.value,
    category: selectedCategory.value,
    condition: selectedCondition.value,
  }, { preserveState: true, replace: true });
}

function resetFilter() {
  search.value = '';
  selectedCategory.value = '';
  selectedCondition.value = '';
  applyFilter();
}

// Modal State & Forms
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedItem = ref(null);

const createForm = useForm({
  name: '',
  category: 'fasilitas_umum',
  location: '',
  quantity: 1,
  condition: 'baik',
  description: '',
});

const editForm = useForm({
  name: '',
  category: '',
  location: '',
  quantity: 1,
  condition: '',
  description: '',
});

function openCreate() {
  createForm.reset();
  createForm.category = 'fasilitas_umum';
  createForm.condition = 'baik';
  createForm.quantity = 1;
  createForm.clearErrors();
  showCreateModal.value = true;
}

function submitCreate() {
  createForm.post('/staf/desa/sarana', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function openEdit(item) {
  selectedItem.value = item;
  editForm.name = item.name;
  editForm.category = item.category;
  editForm.location = item.location || '';
  editForm.quantity = item.quantity;
  editForm.condition = item.condition;
  editForm.description = item.description || '';
  editForm.clearErrors();
  showEditModal.value = true;
}

function submitEdit() {
  if (!selectedItem.value) return;
  editForm.put(`/staf/desa/sarana/${selectedItem.value.id}`, {
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
    title: 'Hapus Sarana & Prasarana',
    message: `Apakah Anda yakin ingin menghapus data sarana "${item.name}"?`,
    confirmText: 'Ya, Hapus Data',
    danger: true,
    action: () => {
      router.delete(`/staf/desa/sarana/${item.id}`, {
        onSuccess: () => {
          confirmDialog.value.show = false;
        },
      });
    },
  };
}

function getConditionBadgeClass(condition) {
  switch (condition) {
    case 'baik':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
    case 'rusak_ringan':
      return 'bg-amber-50 text-amber-700 border-amber-200/80';
    case 'rusak_berat':
      return 'bg-rose-50 text-rose-700 border-rose-200/80';
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200';
  }
}
</script>

<template>
  <Head title="Kelola Sarana & Prasarana" />

  <component :is="currentLayout">
    <template #header>Kelola Sarana & Prasarana</template>

    <div class="space-y-6">
      <!-- Top Action & Filter Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
        <div class="flex flex-wrap items-center gap-3">
          <div class="relative w-full sm:w-60">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              🔍
            </span>
            <input
              type="text"
              v-model="search"
              @keyup.enter="applyFilter"
              placeholder="Cari sarana atau lokasi..."
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

          <select
            v-model="selectedCondition"
            @change="applyFilter"
            class="px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 shadow-2xs bg-white text-slate-700 w-full sm:w-auto"
          >
            <option value="">Semua Kondisi</option>
            <option v-for="(label, key) in conditions" :key="key" :value="key">{{ label }}</option>
          </select>

          <button
            @click="applyFilter"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shadow-2xs"
          >
            Filter
          </button>
          <button
            v-if="search || selectedCategory || selectedCondition"
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
          <span>Tambah Sarana</span>
        </button>
      </div>

      <!-- Data Table -->
      <DataTable :pagination="facilities" :is-empty="facilities.data.length === 0" empty-text="Tidak ada data sarana yang cocok dengan filter.">
        <template #header>
          <tr>
            <th class="px-5 py-3.5">Nama Fasilitas & Lokasi</th>
            <th class="px-5 py-3.5">Kategori</th>
            <th class="px-5 py-3.5 text-center">Jumlah Unit</th>
            <th class="px-5 py-3.5 text-center">Kondisi</th>
            <th class="px-5 py-3.5">Keterangan</th>
            <th class="px-5 py-3.5 text-right">Aksi</th>
          </tr>
        </template>

        <tr v-for="item in facilities.data" :key="item.id" class="hover:bg-teal-50/20 transition group">
          <td class="px-5 py-4">
            <p class="font-bold text-slate-800 text-sm group-hover:text-teal-700 transition">{{ item.name }}</p>
            <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
              <span>📍</span>
              <span>{{ item.location || 'Wilayah Desa' }}</span>
            </p>
          </td>

          <td class="px-5 py-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200/80">
              {{ categories[item.category] || item.category }}
            </span>
          </td>

          <td class="px-5 py-4 text-center font-bold text-slate-700 text-sm">
            {{ item.quantity }}
          </td>

          <td class="px-5 py-4 text-center">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border"
              :class="getConditionBadgeClass(item.condition)"
            >
              {{ conditions[item.condition] || item.condition }}
            </span>
          </td>

          <td class="px-5 py-4 text-xs text-slate-600 max-w-xs">
            <p class="line-clamp-2 leading-relaxed">{{ item.description || '-' }}</p>
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

    <!-- Modal Tambah Sarana -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Tambah Sarana & Prasarana</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <FormField label="Nama Sarana / Prasarana" id="fc_name" :error="createForm.errors.name" required>
            <input
              id="fc_name"
              type="text"
              v-model="createForm.name"
              placeholder="Contoh: Puskesmas Pembantu Gelam Jaya"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Kategori" id="fc_cat" :error="createForm.errors.category" required>
              <select
                id="fc_cat"
                v-model="createForm.category"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>

            <FormField label="Lokasi / Dusun" id="fc_loc" :error="createForm.errors.location">
              <input
                id="fc_loc"
                type="text"
                v-model="createForm.location"
                placeholder="Contoh: Jl. Kesehatan, Dusun Sukamaju"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Jumlah Unit" id="fc_qty" :error="createForm.errors.quantity" required>
              <input
                id="fc_qty"
                type="number"
                min="1"
                v-model="createForm.quantity"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Kondisi Fisik" id="fc_cond" :error="createForm.errors.condition" required>
              <select
                id="fc_cond"
                v-model="createForm.condition"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in conditions" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>
          </div>

          <FormField label="Keterangan / Fungsi Fasilitas" id="fc_desc" :error="createForm.errors.description">
            <textarea
              id="fc_desc"
              rows="3"
              v-model="createForm.description"
              placeholder="Keterangan tambahan seputar sarana dan prasarana ini..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
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
              <span v-else>Simpan Sarana</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Sarana -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Ubah Data Sarana</h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <FormField label="Nama Sarana / Prasarana" id="fe_name" :error="editForm.errors.name" required>
            <input
              id="fe_name"
              type="text"
              v-model="editForm.name"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Kategori" id="fe_cat" :error="editForm.errors.category" required>
              <select
                id="fe_cat"
                v-model="editForm.category"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>

            <FormField label="Lokasi / Dusun" id="fe_loc" :error="editForm.errors.location">
              <input
                id="fe_loc"
                type="text"
                v-model="editForm.location"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Jumlah Unit" id="fe_qty" :error="editForm.errors.quantity" required>
              <input
                id="fe_qty"
                type="number"
                min="1"
                v-model="editForm.quantity"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Kondisi Fisik" id="fe_cond" :error="editForm.errors.condition" required>
              <select
                id="fe_cond"
                v-model="editForm.condition"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white"
              >
                <option v-for="(label, key) in conditions" :key="key" :value="key">{{ label }}</option>
              </select>
            </FormField>
          </div>

          <FormField label="Keterangan / Fungsi Fasilitas" id="fe_desc" :error="editForm.errors.description">
            <textarea
              id="fe_desc"
              rows="3"
              v-model="editForm.description"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
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

