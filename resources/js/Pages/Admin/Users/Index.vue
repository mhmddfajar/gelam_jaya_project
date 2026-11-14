<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ search: '', role: '', is_active: '' }),
  },
  roles: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// Search & Filter state
const search = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');
const selectedStatus = ref(props.filters.is_active ?? '');

function applyFilter() {
  router.get(
    '/admin/pengguna',
    {
      search: search.value,
      role: selectedRole.value,
      is_active: selectedStatus.value,
    },
    { preserveState: true, replace: true }
  );
}

function resetFilter() {
  search.value = '';
  selectedRole.value = '';
  selectedStatus.value = '';
  applyFilter();
}

// Modal state for Create / Edit
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

const createForm = useForm({
  name: '',
  email: '',
  password: '',
  role: 'staf',
  is_active: true,
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
  role: 'staf',
  is_active: true,
});

function openCreateModal() {
  createForm.reset();
  createForm.clearErrors();
  showCreateModal.value = true;
}

function submitCreate() {
  createForm.post('/admin/pengguna', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function openEditModal(user) {
  selectedUser.value = user;
  editForm.name = user.name;
  editForm.email = user.email;
  editForm.role = user.role;
  editForm.is_active = Boolean(user.is_active);
  editForm.password = '';
  editForm.clearErrors();
  showEditModal.value = true;
}

function submitEdit() {
  if (!selectedUser.value) return;
  editForm.put(`/admin/pengguna/${selectedUser.value.id}`, {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
}

// Dialog state for Toggle Active, Reset Password, Delete
const confirmDialog = ref({
  show: false,
  title: '',
  message: '',
  confirmText: '',
  danger: false,
  action: null,
});

function confirmToggleActive(user) {
  if (user.id === currentUser.value?.id) {
    alert('Admin tidak bisa menonaktifkan akunnya sendiri.');
    return;
  }

  const actionName = user.is_active ? 'menonaktifkan' : 'mengaktifkan';
  confirmDialog.value = {
    show: true,
    title: `Konfirmasi ${actionName.toUpperCase()}`,
    message: `Apakah Anda yakin ingin ${actionName} akun pengguna "${user.name}"?`,
    confirmText: user.is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
    danger: user.is_active,
    action: () => {
      router.patch(`/admin/pengguna/${user.id}/toggle-active`, {}, {
        onSuccess: () => (confirmDialog.value.show = false),
      });
    },
  };
}

function confirmResetPassword(user) {
  confirmDialog.value = {
    show: true,
    title: 'Reset Password Pengguna',
    message: `Apakah Anda yakin ingin mereset password pengguna "${user.name}" ke password default (password123)?`,
    confirmText: 'Ya, Reset Password',
    danger: true,
    action: () => {
      router.post(`/admin/pengguna/${user.id}/reset-password`, {}, {
        onSuccess: () => (confirmDialog.value.show = false),
      });
    },
  };
}

function confirmDelete(user) {
  if (user.id === currentUser.value?.id) {
    alert('Anda tidak dapat menghapus akun Anda sendiri.');
    return;
  }

  confirmDialog.value = {
    show: true,
    title: 'Hapus Pengguna',
    message: `Apakah Anda yakin ingin menghapus akun pengguna "${user.name}" (${user.email})? Aksi ini tidak dapat dibatalkan.`,
    confirmText: 'Ya, Hapus Permanen',
    danger: true,
    action: () => {
      router.delete(`/admin/pengguna/${user.id}`, {
        onSuccess: () => (confirmDialog.value.show = false),
      });
    },
  };
}
</script>

<template>
  <Head title="Kelola Pengguna" />

  <AdminLayout>
    <template #header>Manajemen Akun Pengguna</template>

    <div class="space-y-6">
      <!-- Top Action & Filter Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
          <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              🔍
            </span>
            <input
              type="text"
              v-model="search"
              @keyup.enter="applyFilter"
              placeholder="Cari nama atau email..."
              class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs transition"
            />
          </div>

          <select
            v-model="selectedRole"
            @change="applyFilter"
            class="w-full sm:w-40 px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white text-slate-700"
          >
            <option value="">Semua Peran</option>
            <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
          </select>

          <select
            v-model="selectedStatus"
            @change="applyFilter"
            class="w-full sm:w-36 px-3.5 py-2 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white text-slate-700"
          >
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>

          <button
            type="button"
            @click="applyFilter"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shadow-2xs"
          >
            Filter
          </button>
          <button
            v-if="search || selectedRole || selectedStatus !== ''"
            type="button"
            @click="resetFilter"
            class="text-xs text-slate-500 hover:text-slate-800 underline font-medium transition"
          >
            Reset
          </button>
        </div>

        <button
          type="button"
          @click="openCreateModal"
          class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 shrink-0"
        >
          <span class="text-base leading-none">＋</span>
          <span>Tambah Pengguna</span>
        </button>
      </div>

      <!-- Users Table -->
      <DataTable :pagination="users" :is-empty="users.data.length === 0" empty-text="Tidak ada pengguna yang cocok dengan kriteria pencarian.">
        <template #header>
          <tr>
            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & Email</th>
            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Peran</th>
            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tautan Penduduk</th>
            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </template>

        <tr v-for="u in users.data" :key="u.id" class="hover:bg-teal-50/20 transition group">
          <td class="px-6 py-4">
            <div class="font-bold text-slate-800 flex items-center gap-2 group-hover:text-teal-700 transition">
              <span>{{ u.name }}</span>
              <span v-if="u.id === currentUser?.id" class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-extrabold">
                Anda
              </span>
            </div>
            <div class="text-xs text-slate-500 font-mono mt-0.5 tracking-tight">{{ u.email }}</div>
          </td>

          <td class="px-6 py-4 capitalize text-sm">
            <span
              class="px-2.5 py-1 rounded-lg text-xs font-semibold"
              :class="{
                'bg-purple-50 text-purple-800 border border-purple-200/70': u.role === 'admin',
                'bg-teal-50 text-teal-800 border border-teal-200/70': u.role === 'staf',
                'bg-emerald-50 text-emerald-800 border border-emerald-200/70': u.role === 'warga',
              }"
            >
              {{ u.role }}
            </span>
          </td>

          <td class="px-6 py-4 text-xs text-slate-600">
            <div v-if="u.resident" class="flex flex-col">
              <span class="font-semibold text-slate-800">{{ u.resident.name }}</span>
              <span class="font-mono text-[11px] text-slate-400">NIK: {{ u.resident.nik }}</span>
            </div>
            <span v-else class="text-slate-400 italic">Tidak tertaut</span>
          </td>

          <td class="px-6 py-4">
            <StatusBadge
              :status="u.is_active ? 'active' : 'inactive'"
              :label="u.is_active ? 'Aktif' : 'Nonaktif'"
            />
          </td>

          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1.5 flex-wrap">
              <button
                type="button"
                @click="openEditModal(u)"
                class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition text-xs font-semibold shadow-2xs"
                title="Edit Data Pengguna"
              >
                Edit
              </button>

              <button
                v-if="u.id !== currentUser?.id"
                type="button"
                @click="confirmToggleActive(u)"
                class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition shadow-2xs"
                :class="u.is_active
                  ? 'border-amber-200/80 text-amber-700 bg-amber-50/60 hover:bg-amber-100'
                  : 'border-emerald-200/80 text-emerald-700 bg-emerald-50/60 hover:bg-emerald-100'"
                :title="u.is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun'"
              >
                {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>

              <button
                type="button"
                @click="confirmResetPassword(u)"
                class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition text-xs font-semibold shadow-2xs"
                title="Reset Password ke Default"
              >
                Reset Sandi
              </button>

              <button
                v-if="u.id !== currentUser?.id"
                type="button"
                @click="confirmDelete(u)"
                class="px-3 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition text-xs font-semibold shadow-2xs"
                title="Hapus Akun Pengguna"
              >
                Hapus
              </button>
            </div>
          </td>
        </tr>
      </DataTable>
    </div>

    <!-- Modal Tambah Pengguna -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="text-lg font-bold text-slate-900">Tambah Akun Pengguna</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <FormField label="Nama Lengkap" id="create_name" :error="createForm.errors.name" required>
            <input
              id="create_name"
              type="text"
              v-model="createForm.name"
              required
              placeholder="Contoh: Ahmad Fauzi"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <FormField label="Alamat Email" id="create_email" :error="createForm.errors.email" required>
            <input
              id="create_email"
              type="email"
              v-model="createForm.email"
              required
              placeholder="email@gelamjaya.test"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <FormField label="Password Awal" id="create_password" :error="createForm.errors.password" required help="Minimal 8 karakter">
            <input
              id="create_password"
              type="password"
              v-model="createForm.password"
              required
              placeholder="Minimal 8 karakter"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Peran (Role)" id="create_role" :error="createForm.errors.role" required>
              <select
                id="create_role"
                v-model="createForm.role"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white"
              >
                <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
              </select>
            </FormField>

            <FormField label="Status Akun" id="create_status">
              <label class="flex items-center gap-2 mt-2.5 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="createForm.is_active"
                  class="w-4 h-4 text-emerald-600 rounded-sm focus:ring-emerald-500"
                />
                <span class="text-sm text-slate-700 font-medium">Akun Aktif</span>
              </label>
            </FormField>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="createForm.processing"
              class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md transition disabled:opacity-50"
            >
              <span v-if="createForm.processing">Menyimpan...</span>
              <span v-else>Simpan Pengguna</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="text-lg font-bold text-slate-900">Ubah Data Pengguna</h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <FormField label="Nama Lengkap" id="edit_name" :error="editForm.errors.name" required>
            <input
              id="edit_name"
              type="text"
              v-model="editForm.name"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <FormField label="Alamat Email" id="edit_email" :error="editForm.errors.email" required>
            <input
              id="edit_email"
              type="email"
              v-model="editForm.email"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <FormField label="Password Baru (Opsional)" id="edit_password" :error="editForm.errors.password" help="Kosongkan jika tidak ingin mengubah password">
            <input
              id="edit_password"
              type="password"
              v-model="editForm.password"
              placeholder="Minimal 8 karakter (opsional)"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Peran (Role)" id="edit_role" :error="editForm.errors.role" required>
              <select
                id="edit_role"
                v-model="editForm.role"
                required
                :disabled="selectedUser?.id === currentUser?.id"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 shadow-xs bg-white disabled:bg-slate-100"
              >
                <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
              </select>
            </FormField>

            <FormField label="Status Akun" id="edit_status">
              <label class="flex items-center gap-2 mt-2.5 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="editForm.is_active"
                  :disabled="selectedUser?.id === currentUser?.id"
                  class="w-4 h-4 text-emerald-600 rounded-sm focus:ring-emerald-500 disabled:opacity-50"
                />
                <span class="text-sm text-slate-700 font-medium">Akun Aktif</span>
              </label>
            </FormField>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showEditModal = false"
              class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="editForm.processing"
              class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md transition disabled:opacity-50"
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
  </AdminLayout>
</template>

