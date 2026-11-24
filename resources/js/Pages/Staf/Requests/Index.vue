<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentLayout = computed(() => (user.value?.role === 'admin' ? AdminLayout : StafLayout));

const props = defineProps({
  requests: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ search: '', status: '', channel: '' }),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, diajukan: 0, diproses: 0, selesai: 0, ditolak: 0, daring: 0, luring: 0 }),
  },
  statuses: {
    type: Array,
    default: () => [],
  },
  channels: {
    type: Array,
    default: () => [],
  },
});

const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedChannel = ref(props.filters.channel || '');

let searchTimeout = null;

const applyFilters = () => {
  router.get(
    '/staf/pengajuan',
    {
      search: search.value || undefined,
      status: selectedStatus.value || undefined,
      channel: selectedChannel.value || undefined,
    },
    { preserveState: true, replace: true }
  );
};

watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 400);
});

watch([selectedStatus, selectedChannel], () => {
  applyFilters();
});

const resetFilters = () => {
  search.value = '';
  selectedStatus.value = '';
  selectedChannel.value = '';
  router.get('/staf/pengajuan', {}, { replace: true });
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
  <Head title="Antrean Pengajuan Surat" />

  <component :is="currentLayout">
    <template #header>Antrean Layanan Pengajuan</template>

    <div class="space-y-6">
      <!-- Top Action & Stat Cards -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-black text-slate-900 tracking-tight">Antrean Pengajuan Surat</h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Kelola dan verifikasi permohonan surat administrasi warga baik melalui jalur daring maupun luring.
          </p>
        </div>

        <Link
          href="/staf/pengajuan/baru"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm hover:shadow transition shrink-0"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Input Layanan Luring</span>
        </Link>
      </div>

      <!-- Quick Metrics Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-slate-400 block uppercase">Total Masuk</span>
          <span class="text-xl font-black text-slate-900 mt-1 block">{{ stats.total || 0 }}</span>
          <span class="text-[10px] text-slate-500 mt-0.5 block">Daring: {{ stats.daring }} • Luring: {{ stats.luring }}</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-blue-600 block uppercase">Diajukan</span>
          <span class="text-xl font-black text-blue-900 mt-1 block">{{ stats.diajukan || 0 }}</span>
          <span class="text-[10px] text-blue-600/80 mt-0.5 block">Menunggu verifikasi</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-indigo-600 block uppercase">Diverifikasi</span>
          <span class="text-xl font-black text-indigo-900 mt-1 block">{{ stats.diverifikasi || 0 }}</span>
          <span class="text-[10px] text-indigo-600/80 mt-0.5 block">Siap diproses</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-amber-600 block uppercase">Diproses</span>
          <span class="text-xl font-black text-amber-900 mt-1 block">{{ stats.diproses || 0 }}</span>
          <span class="text-[10px] text-amber-600/80 mt-0.5 block">Penyusunan naskah</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-emerald-600 block uppercase">Selesai</span>
          <span class="text-xl font-black text-emerald-900 mt-1 block">{{ stats.selesai || 0 }}</span>
          <span class="text-[10px] text-emerald-600/80 mt-0.5 block">Layanan tuntas</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-rose-50/60 border border-rose-200/80 shadow-2xs">
          <span class="text-[11px] font-bold text-rose-600 block uppercase">Ditolak</span>
          <span class="text-xl font-black text-rose-900 mt-1 block">{{ stats.ditolak || 0 }}</span>
          <span class="text-[10px] text-rose-600/80 mt-0.5 block">Berkas tidak sah</span>
        </div>
      </div>

      <!-- Filter Controls Bar -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-stretch md:items-center gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input
            v-model="search"
            type="text"
            placeholder="Cari no. registrasi, nama pemohon, NIK, jenis surat..."
            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition"
          />
        </div>

        <!-- Filter Status -->
        <div class="w-full md:w-44">
          <select
            v-model="selectedStatus"
            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition bg-white"
          >
            <option value="">Semua Status</option>
            <option v-for="st in statuses" :key="st.value" :value="st.value">
              {{ st.label }}
            </option>
          </select>
        </div>

        <!-- Filter Channel -->
        <div class="w-full md:w-36">
          <select
            v-model="selectedChannel"
            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition bg-white"
          >
            <option value="">Semua Jalur</option>
            <option value="daring">Daring (Online)</option>
            <option value="luring">Luring (Kantor)</option>
          </select>
        </div>

        <!-- Reset Button -->
        <button
          v-if="search || selectedStatus || selectedChannel"
          type="button"
          @click="resetFilters"
          class="px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition shrink-0"
        >
          Reset Filter
        </button>
      </div>

      <!-- Requests Data Table -->
      <DataTable
        :pagination="requests"
        :is-empty="requests.data.length === 0"
        empty-text="Tidak ada pengajuan surat yang sesuai dengan filter pencarian."
      >
        <template #header>
          <tr>
            <th class="px-5 py-3.5">No. Registrasi</th>
            <th class="px-5 py-3.5">Pemohon</th>
            <th class="px-5 py-3.5">Jenis Surat & Keperluan</th>
            <th class="px-5 py-3.5">Jalur</th>
            <th class="px-5 py-3.5">Status</th>
            <th class="px-5 py-3.5 text-right">Aksi</th>
          </tr>
        </template>

        <tr
          v-for="req in requests.data"
          :key="req.id"
          class="hover:bg-slate-50/70 transition-colors"
        >
          <!-- No Registrasi & Tanggal -->
          <td class="px-5 py-4 whitespace-nowrap">
            <Link
              :href="`/staf/pengajuan/${req.id}`"
              class="font-mono text-xs font-black text-teal-700 hover:text-teal-900 hover:underline block"
            >
              {{ req.registration_no }}
            </Link>
            <span class="text-[11px] text-slate-400 block mt-0.5">
              {{ formatDate(req.created_at) }}
            </span>
          </td>

          <!-- Pemohon & NIK -->
          <td class="px-5 py-4">
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-900 text-xs block">
                {{ req.resident?.name || '-' }}
              </span>
              <span
                v-if="req.resident && !req.resident.is_verified"
                title="Data diri warga belum terverifikasi oleh staf"
                class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 whitespace-nowrap"
              >
                Belum Verif
              </span>
            </div>
            <span class="font-mono text-[11px] text-slate-500 block">
              NIK: {{ req.resident?.nik || '-' }}
            </span>
            <span v-if="req.resident?.hamlet" class="text-[10px] text-slate-400 block">
              Dusun {{ req.resident.hamlet.name }} • RT {{ req.resident.rt }}/RW {{ req.resident.rw }}
            </span>
          </td>

          <!-- Jenis Surat & Keperluan -->
          <td class="px-5 py-4 max-w-xs">
            <span class="font-bold text-slate-800 text-xs block">
              [{{ req.letter_type?.code }}] {{ req.letter_type?.name }}
            </span>
            <p class="text-xs text-slate-500 truncate mt-0.5" :title="req.purpose">
              {{ req.purpose }}
            </p>
          </td>

          <!-- Jalur (Badge) -->
          <td class="px-5 py-4 whitespace-nowrap">
            <span
              class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold"
              :class="(req.channel?.value || req.channel) === 'daring'
                ? 'bg-sky-50 text-sky-700 border border-sky-200'
                : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
            >
              {{ (req.channel?.value || req.channel) === 'daring' ? '🌐 Daring' : '🏢 Luring' }}
            </span>
          </td>

          <!-- Status Badge -->
          <td class="px-5 py-4 whitespace-nowrap">
            <StatusBadge
              :status="req.status?.value || req.status"
              :label="req.status?.label"
            />
          </td>

          <!-- Actions -->
          <td class="px-5 py-4 text-right whitespace-nowrap">
            <Link
              :href="`/staf/pengajuan/${req.id}`"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-800 font-bold text-xs border border-slate-200 hover:border-teal-200 transition shadow-2xs"
            >
              <span>Proses</span>
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </td>
        </tr>
      </DataTable>
    </div>
  </component>
</template>
