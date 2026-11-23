<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
  letterTypes: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ search: '', is_active: '' }),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, active: 0, inactive: 0 }),
  },
});

const searchQuery = ref(props.filters.search || '');
const activeFilter = ref(props.filters.is_active ?? '');

const showDeleteModal = ref(false);
const itemToDelete = ref(null);

const showDetailModal = ref(false);
const selectedItem = ref(null);

const handleSearch = () => {
  router.get(
    '/admin/jenis-surat',
    {
      search: searchQuery.value || undefined,
      is_active: activeFilter.value !== '' ? activeFilter.value : undefined,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilter = () => {
  searchQuery.value = '';
  activeFilter.value = '';
  handleSearch();
};

const confirmDelete = (item) => {
  itemToDelete.value = item;
  showDeleteModal.value = true;
};

const executeDelete = () => {
  if (!itemToDelete.value) return;
  router.delete(`/admin/jenis-surat/${itemToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      itemToDelete.value = null;
    },
  });
};

const toggleStatus = (item) => {
  router.patch(
    `/admin/jenis-surat/${item.id}/toggle-active`,
    {},
    { preserveScroll: true }
  );
};

const openDetail = (item) => {
  selectedItem.value = item;
  showDetailModal.value = true;
};
</script>

<template>
  <Head title="Kelola Jenis Surat & Persyaratan" />

  <AdminLayout>
    <div class="space-y-6">
      <!-- Header Page -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
            <svg class="w-7 h-7 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Master Jenis Surat & Persyaratan</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Kelola katalog jenis surat permohonan warga, syarat berkas lampiran, dan skema formulir isian dinamis.
          </p>
        </div>

        <Link
          href="/admin/jenis-surat/create"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-[0.98]"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah Jenis Surat</span>
        </Link>
      </div>

      <!-- Quick Stats Cards (Clean Soft Light) -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400">Total Jenis Surat</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-emerald-600">Layanan Aktif</p>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ stats.active }}</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400">Nonaktif / Draft</p>
            <p class="text-2xl font-black text-slate-600 mt-1">{{ stats.inactive }}</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Filter & Search Toolbar -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row gap-3">
          <!-- Search Input -->
          <div class="relative flex-1">
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              placeholder="Cari kode (SKU, SKD) atau nama surat..."
              class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Status Dropdown -->
          <select
            v-model="activeFilter"
            @change="handleSearch"
            class="px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-700"
          >
            <option value="">Semua Status</option>
            <option value="true">Hanya Aktif</option>
            <option value="false">Hanya Nonaktif</option>
          </select>

          <!-- Search Button -->
          <button
            type="button"
            @click="handleSearch"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs"
          >
            Cari
          </button>

          <!-- Reset Filter -->
          <button
            v-if="searchQuery || activeFilter !== ''"
            type="button"
            @click="resetFilter"
            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-medium transition"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Main Data Table -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm text-slate-600">
            <thead class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-5 py-3.5">Kode</th>
                <th scope="col" class="px-5 py-3.5">Nama Jenis Surat</th>
                <th scope="col" class="px-5 py-3.5">Syarat Berkas</th>
                <th scope="col" class="px-5 py-3.5">Kolom Isian Form</th>
                <th scope="col" class="px-5 py-3.5">Status</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr
                v-for="item in letterTypes.data"
                :key="item.id"
                class="hover:bg-slate-50/70 transition"
              >
                <!-- Kode -->
                <td class="px-5 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold bg-teal-50 text-teal-700 border border-teal-200/70">
                    {{ item.code }}
                  </span>
                </td>

                <!-- Nama Surat & Deskripsi -->
                <td class="px-5 py-4">
                  <div class="font-bold text-slate-900">{{ item.name }}</div>
                  <div class="text-[11px] text-slate-400 max-w-sm truncate mt-0.5">
                    {{ item.description || 'Tidak ada deskripsi tambahan.' }}
                  </div>
                </td>

                <!-- Syarat Berkas -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5 flex-wrap max-w-xs">
                    <span
                      v-if="!item.requirements || item.requirements.length === 0"
                      class="text-xs text-slate-400 italic"
                    >
                      Tidak ada syarat
                    </span>
                    <template v-else>
                      <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                        {{ item.requirements.length }} Dokumen
                      </span>
                      <button
                        type="button"
                        @click="openDetail(item)"
                        class="text-[11px] text-teal-600 hover:text-teal-700 font-bold underline"
                      >
                        Lihat
                      </button>
                    </template>
                  </div>
                </td>

                <!-- Kolom Isian Form Dinamis -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-1.5">
                    <span
                      v-if="!item.form_fields || item.form_fields.length === 0"
                      class="text-xs text-slate-400 italic"
                    >
                      Bawaan standar
                    </span>
                    <template v-else>
                      <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                        {{ item.form_fields.length }} Field
                      </span>
                      <button
                        type="button"
                        @click="openDetail(item)"
                        class="text-[11px] text-teal-600 hover:text-teal-700 font-bold underline"
                      >
                        Lihat
                      </button>
                    </template>
                  </div>
                </td>

                <!-- Status Aktif / Toggle -->
                <td class="px-5 py-4 whitespace-nowrap">
                  <button
                    type="button"
                    @click="toggleStatus(item)"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition"
                    :class="item.is_active
                      ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100'
                      : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200'"
                    :title="item.is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"
                    ></span>
                    <span>{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                  </button>
                </td>

                <!-- Aksi -->
                <td class="px-5 py-4 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="openDetail(item)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition"
                      title="Lihat Rincian"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>

                    <Link
                      :href="`/admin/jenis-surat/${item.id}/edit`"
                      class="p-1.5 rounded-lg text-teal-600 hover:text-teal-700 hover:bg-teal-50 transition"
                      title="Edit Jenis Surat"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </Link>

                    <button
                      type="button"
                      @click="confirmDelete(item)"
                      class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition"
                      title="Hapus Jenis Surat"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="letterTypes.data.length === 0">
                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                  </div>
                  <p class="font-bold text-slate-700">Tidak ada jenis surat ditemukan</p>
                  <p class="text-xs text-slate-400 mt-1">Gunakan kata kunci lain atau tambahkan jenis surat baru.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="letterTypes.links && letterTypes.links.length > 3"
          class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500"
        >
          <span>
            Menampilkan {{ letterTypes.from || 0 }} – {{ letterTypes.to || 0 }} dari {{ letterTypes.total }} surat
          </span>

          <div class="flex items-center gap-1">
            <template v-for="(link, idx) in letterTypes.links" :key="idx">
              <Link
                v-if="link.url"
                :href="link.url"
                class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                :class="link.active
                  ? 'bg-teal-600 text-white border-teal-600'
                  : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                v-html="link.label"
                preserve-scroll
              />
              <span
                v-else
                class="px-3 py-1.5 text-slate-300 border border-transparent text-xs"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Detail Rincian Surat -->
    <div
      v-if="showDetailModal && selectedItem"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition"
    >
      <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-xl border border-slate-200 space-y-5 animate-smooth-enter">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200">
              {{ selectedItem.code }}
            </span>
            <h2 class="text-base sm:text-lg font-black text-slate-900">
              {{ selectedItem.name }}
            </h2>
          </div>
          <button
            type="button"
            @click="showDetailModal = false"
            class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1 text-xs sm:text-sm">
          <div>
            <span class="font-bold text-slate-700 block mb-1">Deskripsi Surat:</span>
            <p class="text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
              {{ selectedItem.description || 'Tidak ada deskripsi.' }}
            </p>
          </div>

          <!-- Syarat Berkas -->
          <div>
            <span class="font-bold text-slate-700 block mb-2 flex items-center gap-1.5">
              <span>Berkas Persyaratan Wajib ({{ selectedItem.requirements?.length || 0 }}):</span>
            </span>
            <div v-if="selectedItem.requirements && selectedItem.requirements.length > 0" class="space-y-1.5">
              <div
                v-for="(req, i) in selectedItem.requirements"
                :key="i"
                class="flex items-center gap-2 p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/70 text-slate-800"
              >
                <span class="w-5 h-5 rounded-full bg-teal-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                  {{ i + 1 }}
                </span>
                <span class="font-semibold">{{ req }}</span>
              </div>
            </div>
            <p v-else class="text-slate-400 italic">Tidak ada berkas persyaratan yang ditentukan.</p>
          </div>

          <!-- Kolom Formulir Tambahan -->
          <div>
            <span class="font-bold text-slate-700 block mb-2">
              Kolom Isian Formulir Tambahan ({{ selectedItem.form_fields?.length || 0 }}):
            </span>
            <div v-if="selectedItem.form_fields && selectedItem.form_fields.length > 0" class="space-y-2">
              <div
                v-for="(field, idx) in selectedItem.form_fields"
                :key="idx"
                class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3"
              >
                <div>
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800">{{ field.label }}</span>
                    <span class="font-mono text-[10px] text-slate-400">({{ field.name }})</span>
                  </div>
                  <div v-if="field.options && field.options.length > 0" class="text-[11px] text-slate-500 mt-1">
                    Pilihan: {{ field.options.join(', ') }}
                  </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 uppercase">
                    {{ field.type }}
                  </span>
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold"
                    :class="field.required ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500'"
                  >
                    {{ field.required ? 'Wajib' : 'Opsional' }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-slate-400 italic">Hanya menggunakan isian baku standar (Keperluan Surat).</p>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
          <Link
            :href="`/admin/jenis-surat/${selectedItem.id}/edit`"
            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition"
          >
            Edit Jenis Surat
          </Link>
          <button
            type="button"
            @click="showDetailModal = false"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmDialog
      :show="showDeleteModal"
      title="Hapus Jenis Surat"
      :message="`Apakah Anda yakin ingin menghapus jenis surat '${itemToDelete?.name}' (${itemToDelete?.code})? Tindakan ini tidak dapat dibatalkan.`"
      confirm-text="Ya, Hapus"
      cancel-text="Batal"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />
  </AdminLayout>
</template>
