<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WargaLayout from '@/Layouts/WargaLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  requests: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ status: '', search: '' }),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, active: 0, completed: 0, rejected: 0 }),
  },
});

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const handleFilter = () => {
  router.get(
    '/warga/pengajuan',
    {
      search: searchQuery.value || undefined,
      status: statusFilter.value || undefined,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilter = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  handleFilter();
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const d = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(d);
};
</script>

<template>
  <Head title="Pengajuan Surat Saya - Portal Warga" />

  <WargaLayout>
    <div class="space-y-6">
      <!-- Header Page -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
            <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Daftar Pengajuan Surat Saya</span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Pantau status verifikasi, perbaikan berkas, dan pengambilan surat dinas Anda secara berkala.
          </p>
        </div>

        <Link
          href="/warga/pengajuan/baru"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-[0.98]"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Buat Pengajuan Baru</span>
        </Link>
      </div>

      <!-- Quick Stats Cards (Clean Soft Light) -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Permohonan</p>
          <p class="text-2xl font-black text-slate-900 mt-1">{{ stats.total }}</p>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
          <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Sedang Diproses</p>
          <p class="text-2xl font-black text-amber-600 mt-1">{{ stats.active }}</p>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
          <p class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Selesai / Terbit</p>
          <p class="text-2xl font-black text-emerald-700 mt-1">{{ stats.completed }}</p>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
          <p class="text-[11px] font-semibold text-rose-500 uppercase tracking-wider">Ditolak</p>
          <p class="text-2xl font-black text-rose-600 mt-1">{{ stats.rejected }}</p>
        </div>
      </div>

      <!-- Filter Toolbar -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row gap-3">
          <!-- Search Input -->
          <div class="relative flex-1">
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="handleFilter"
              placeholder="Cari no. registrasi (REG-...), jenis surat, atau keperluan..."
              class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Status Dropdown -->
          <select
            v-model="statusFilter"
            @change="handleFilter"
            class="px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700"
          >
            <option value="">Semua Status</option>
            <option value="diajukan">Diajukan</option>
            <option value="perlu_perbaikan">Perlu Perbaikan</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="diproses">Diproses</option>
            <option value="siap_diambil">Siap Diambil</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
          </select>

          <!-- Search Button -->
          <button
            type="button"
            @click="handleFilter"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs"
          >
            Cari
          </button>

          <!-- Reset Filter -->
          <button
            v-if="searchQuery || statusFilter"
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
                <th scope="col" class="px-5 py-3.5">No. Registrasi</th>
                <th scope="col" class="px-5 py-3.5">Jenis Surat</th>
                <th scope="col" class="px-5 py-3.5">Keperluan</th>
                <th scope="col" class="px-5 py-3.5">Tanggal Pengajuan</th>
                <th scope="col" class="px-5 py-3.5">Status</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <tr
                v-for="item in requests.data"
                :key="item.id"
                class="hover:bg-slate-50/70 transition"
              >
                <!-- No. Registrasi -->
                <td class="px-5 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                    {{ item.registration_no }}
                  </span>
                </td>

                <!-- Jenis Surat -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                      {{ item.letter_type?.code }}
                    </span>
                    <span class="font-bold text-slate-900">{{ item.letter_type?.name }}</span>
                  </div>
                </td>

                <!-- Keperluan -->
                <td class="px-5 py-4">
                  <p class="text-xs text-slate-700 max-w-xs truncate font-medium">
                    {{ item.purpose }}
                  </p>
                </td>

                <!-- Tanggal Pengajuan -->
                <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                  {{ formatDate(item.created_at) }}
                </td>

                <!-- Status Badge -->
                <td class="px-5 py-4 whitespace-nowrap">
                  <StatusBadge
                    :status="item.status?.value || item.status"
                    :label="item.status?.label"
                  />
                </td>

                <!-- Aksi -->
                <td class="px-5 py-4 whitespace-nowrap text-right">
                  <Link
                    :href="`/warga/pengajuan/${item.id}`"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs transition border border-transparent hover:border-emerald-200"
                  >
                    <span>Detail</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </td>
              </tr>

              <tr v-if="requests.data.length === 0">
                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <p class="font-bold text-slate-700">Belum ada pengajuan surat</p>
                  <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Anda belum memiliki riwayat pengajuan surat daring. Klik tombol di bawah untuk membuat permohonan baru.
                  </p>
                  <div class="mt-4">
                    <Link
                      href="/warga/pengajuan/baru"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs"
                    >
                      <span>+ Buat Pengajuan Sekarang</span>
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="requests.links && requests.links.length > 3"
          class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500"
        >
          <span>
            Menampilkan {{ requests.from || 0 }} – {{ requests.to || 0 }} dari {{ requests.total }} pengajuan
          </span>

          <div class="flex items-center gap-1">
            <template v-for="(link, idx) in requests.links" :key="idx">
              <Link
                v-if="link.url"
                :href="link.url"
                class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                :class="link.active
                  ? 'bg-emerald-600 text-white border-emerald-600'
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
  </WargaLayout>
</template>
