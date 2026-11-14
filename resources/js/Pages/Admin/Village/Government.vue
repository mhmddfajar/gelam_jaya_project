<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
  officials: {
    type: Array,
    default: () => [],
  },
});

// Visi Misi Form
const visionMissionForm = useForm({
  vision: props.profile.vision || '',
  mission: props.profile.mission || '',
});

function submitVisionMission() {
  visionMissionForm.put('/admin/desa/visi-misi', {
    preserveScroll: true,
  });
}

// Officials Modals & Form
const showCreateOfficialModal = ref(false);
const showEditOfficialModal = ref(false);
const selectedOfficial = ref(null);

const createOfficialForm = useForm({
  name: '',
  position: '',
  period_start: new Date().getFullYear(),
  period_end: new Date().getFullYear() + 6,
  can_sign: false,
  is_active: true,
  sort_order: props.officials.length + 1,
  photo: null,
});

const editOfficialForm = useForm({
  _method: 'PUT',
  name: '',
  position: '',
  period_start: null,
  period_end: null,
  can_sign: false,
  is_active: true,
  sort_order: 0,
  photo: null,
});

function openCreateOfficial() {
  createOfficialForm.reset();
  createOfficialForm.period_start = new Date().getFullYear();
  createOfficialForm.period_end = new Date().getFullYear() + 6;
  createOfficialForm.sort_order = props.officials.length + 1;
  createOfficialForm.is_active = true;
  createOfficialForm.can_sign = false;
  createOfficialForm.clearErrors();
  showCreateOfficialModal.value = true;
}

function submitCreateOfficial() {
  createOfficialForm.post('/admin/desa/perangkat', {
    onSuccess: () => {
      showCreateOfficialModal.value = false;
      createOfficialForm.reset();
    },
  });
}

function openEditOfficial(official) {
  selectedOfficial.value = official;
  editOfficialForm.name = official.name;
  editOfficialForm.position = official.position;
  editOfficialForm.period_start = official.period_start;
  editOfficialForm.period_end = official.period_end;
  editOfficialForm.can_sign = Boolean(official.can_sign);
  editOfficialForm.is_active = Boolean(official.is_active);
  editOfficialForm.sort_order = official.sort_order || 0;
  editOfficialForm.photo = null;
  editOfficialForm.clearErrors();
  showEditOfficialModal.value = true;
}

function submitEditOfficial() {
  if (!selectedOfficial.value) return;
  editOfficialForm.post(`/admin/desa/perangkat/${selectedOfficial.value.id}`, {
    onSuccess: () => {
      showEditOfficialModal.value = false;
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

function confirmDeleteOfficial(official) {
  confirmDialog.value = {
    show: true,
    title: 'Hapus Perangkat Desa',
    message: `Apakah Anda yakin ingin menghapus data perangkat desa "${official.name}" (${official.position})?`,
    confirmText: 'Ya, Hapus',
    danger: true,
    action: () => {
      router.delete(`/admin/desa/perangkat/${official.id}`, {
        onSuccess: () => {
          confirmDialog.value.show = false;
        },
      });
    },
  };
}
</script>

<template>
  <Head title="Visi Misi & Pemerintahan Desa" />

  <AdminLayout>
    <template #header>Visi Misi & Pemerintahan Desa</template>

    <div class="space-y-8">
      <!-- Section 1: Visi & Misi Desa -->
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
              <span>📜</span>
              <span>Visi dan Misi Pemerintah Desa</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
              Visi dan misi kepemimpinan kepala desa yang dipublikasikan pada portal informasi masyarakat.
            </p>
          </div>
        </div>

        <form @submit.prevent="submitVisionMission" class="space-y-5">
          <FormField label="Visi Desa" id="vision" :error="visionMissionForm.errors.vision" help="Rumusan cita-cita masa depan pembangunan Desa Gelam Jaya">
            <textarea
              id="vision"
              rows="3"
              v-model="visionMissionForm.vision"
              placeholder="Tuliskan visi kepemimpinan desa..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            ></textarea>
          </FormField>

          <FormField label="Misi Desa" id="mission" :error="visionMissionForm.errors.mission" help="Langkah-langkah strategis untuk mencapai visi desa (dapat berupa poin-poin bernomor)">
            <textarea
              id="mission"
              rows="5"
              v-model="visionMissionForm.mission"
              placeholder="1. Meningkatkan kualitas pelayanan publik...&#10;2. Mewujudkan tata kelola desa yang bersih..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-sans"
            ></textarea>
          </FormField>

          <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button
              type="submit"
              :disabled="visionMissionForm.processing"
              class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <span v-if="visionMissionForm.processing">Menyimpan...</span>
              <span v-else>Simpan Visi & Misi</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Section 2: Struktur Perangkat Desa (Officials) -->
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
              <span>👥</span>
              <span>Struktur Organisasi & Perangkat Desa</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
              Data aparatur pemerintah desa yang berwenang melayani warga dan menandatangani surat dinas.
            </p>
          </div>

          <button
            @click="openCreateOfficial"
            class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all flex items-center gap-2 justify-center shrink-0"
          >
            <span class="text-base leading-none">＋</span>
            <span>Tambah Perangkat Desa</span>
          </button>
        </div>

        <!-- Officials Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
          <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50/90 text-slate-500 font-bold uppercase text-[11px] tracking-wider border-b border-slate-100">
              <tr>
                <th class="px-5 py-3.5">Urutan</th>
                <th class="px-5 py-3.5">Nama & Jabatan</th>
                <th class="px-5 py-3.5">Masa Jabatan</th>
                <th class="px-5 py-3.5 text-center">Tanda Tangan</th>
                <th class="px-5 py-3.5 text-center">Status</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800">
              <tr v-for="o in officials" :key="o.id" class="hover:bg-teal-50/20 transition group">
                <td class="px-5 py-4 text-xs font-mono font-bold text-slate-400 text-center w-12">
                  #{{ o.sort_order }}
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-xs font-bold text-slate-600 shadow-2xs">
                      <img v-if="o.photo_path" :src="o.photo_path" :alt="o.name" class="w-full h-full object-cover" />
                      <span v-else>{{ o.name.charAt(0) }}</span>
                    </div>
                    <div>
                      <p class="font-bold text-slate-800 text-sm group-hover:text-teal-700 transition">{{ o.name }}</p>
                      <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ o.position }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-xs text-slate-600">
                  <span v-if="o.period_start || o.period_end" class="font-mono">
                    {{ o.period_start || '?' }} – {{ o.period_end || 'Sekarang' }}
                  </span>
                  <span v-else class="text-slate-400 italic">Tidak ada data periode</span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span
                    v-if="o.can_sign"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80"
                  >
                    <span>✍️</span> Penandatangan
                  </span>
                  <span v-else class="text-xs text-slate-400">—</span>
                </td>
                <td class="px-5 py-4 text-center">
                  <StatusBadge
                    :status="o.is_active ? 'active' : 'inactive'"
                    :label="o.is_active ? 'Aktif' : 'Nonaktif'"
                  />
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      @click="openEditOfficial(o)"
                      class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shadow-2xs"
                    >
                      Edit
                    </button>
                    <button
                      @click="confirmDeleteOfficial(o)"
                      class="px-3 py-1.5 rounded-xl border border-rose-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="officials.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                  Belum ada data perangkat desa. Klik tombol Tambah Perangkat Desa untuk menambahkan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Perangkat Desa -->
    <div v-if="showCreateOfficialModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showCreateOfficialModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Tambah Perangkat Desa Baru</h3>
          <button @click="showCreateOfficialModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitCreateOfficial" class="space-y-4">
          <FormField label="Nama Lengkap & Gelar" id="oc_name" :error="createOfficialForm.errors.name" required>
            <input
              id="oc_name"
              type="text"
              v-model="createOfficialForm.name"
              placeholder="Contoh: H. Suryadi Pratama, S.IP"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <FormField label="Jabatan" id="oc_pos" :error="createOfficialForm.errors.position" required>
            <input
              id="oc_pos"
              type="text"
              v-model="createOfficialForm.position"
              placeholder="Contoh: Kepala Desa, Sekretaris Desa"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Tahun Mulai Menjabat" id="oc_pstart" :error="createOfficialForm.errors.period_start">
              <input
                id="oc_pstart"
                type="number"
                v-model="createOfficialForm.period_start"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Tahun Akhir Menjabat" id="oc_pend" :error="createOfficialForm.errors.period_end">
              <input
                id="oc_pend"
                type="number"
                v-model="createOfficialForm.period_end"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Nomor Urut Tampil" id="oc_sort" :error="createOfficialForm.errors.sort_order" help="Urutan 1 untuk Kepala Desa">
              <input
                id="oc_sort"
                type="number"
                v-model="createOfficialForm.sort_order"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Foto Resmi (Opsional)" id="oc_photo" :error="createOfficialForm.errors.photo">
              <input
                id="oc_photo"
                type="file"
                accept="image/*"
                @change="createOfficialForm.photo = $event.target.files[0]"
                class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
              />
            </FormField>
          </div>

          <!-- Checkboxes -->
          <div class="space-y-3 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="createOfficialForm.can_sign"
                class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300"
              />
              <div>
                <span class="text-sm font-bold text-slate-800">Dapat Menandatangani Surat</span>
                <p class="text-xs text-slate-400">Aktifkan jika pejabat ini berhak menjadi penandatangan surat keterangan desa.</p>
              </div>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="createOfficialForm.is_active"
                class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300"
              />
              <div>
                <span class="text-sm font-bold text-slate-800">Status Aktif Menjabat</span>
                <p class="text-xs text-slate-400">Tampilkan pada struktur kepengurusan publik dan menu penandatangan surat.</p>
              </div>
            </label>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showCreateOfficialModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="createOfficialForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="createOfficialForm.processing">Menyimpan...</span>
              <span v-else>Simpan Perangkat Desa</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Perangkat Desa -->
    <div v-if="showEditOfficialModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showEditOfficialModal = false"></div>
      <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-bold text-slate-900">Ubah Data Perangkat Desa</h3>
          <button @click="showEditOfficialModal = false" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form @submit.prevent="submitEditOfficial" class="space-y-4">
          <FormField label="Nama Lengkap & Gelar" id="oe_name" :error="editOfficialForm.errors.name" required>
            <input
              id="oe_name"
              type="text"
              v-model="editOfficialForm.name"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <FormField label="Jabatan" id="oe_pos" :error="editOfficialForm.errors.position" required>
            <input
              id="oe_pos"
              type="text"
              v-model="editOfficialForm.position"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs"
            />
          </FormField>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Tahun Mulai Menjabat" id="oe_pstart" :error="editOfficialForm.errors.period_start">
              <input
                id="oe_pstart"
                type="number"
                v-model="editOfficialForm.period_start"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Tahun Akhir Menjabat" id="oe_pend" :error="editOfficialForm.errors.period_end">
              <input
                id="oe_pend"
                type="number"
                v-model="editOfficialForm.period_end"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Nomor Urut Tampil" id="oe_sort" :error="editOfficialForm.errors.sort_order">
              <input
                id="oe_sort"
                type="number"
                v-model="editOfficialForm.sort_order"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono"
              />
            </FormField>

            <FormField label="Foto Baru (Opsional)" id="oe_photo" :error="editOfficialForm.errors.photo">
              <input
                id="oe_photo"
                type="file"
                accept="image/*"
                @change="editOfficialForm.photo = $event.target.files[0]"
                class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
              />
            </FormField>
          </div>

          <!-- Checkboxes -->
          <div class="space-y-3 pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="editOfficialForm.can_sign"
                class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300"
              />
              <div>
                <span class="text-sm font-bold text-slate-800">Dapat Menandatangani Surat</span>
                <p class="text-xs text-slate-400">Aktifkan jika pejabat ini berhak menjadi penandatangan surat dinas.</p>
              </div>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="editOfficialForm.is_active"
                class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300"
              />
              <div>
                <span class="text-sm font-bold text-slate-800">Status Aktif Menjabat</span>
                <p class="text-xs text-slate-400">Tampilkan pada struktur kepengurusan publik dan kop surat.</p>
              </div>
            </label>
          </div>

          <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <button
              type="button"
              @click="showEditOfficialModal = false"
              class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="editOfficialForm.processing"
              class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition disabled:opacity-50"
            >
              <span v-if="editOfficialForm.processing">Menyimpan...</span>
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

