<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  metrics: {
    type: Object,
    default: () => ({
      today_requests: 0,
      daring_requests: 0,
      luring_requests: 0,
      total_residents: 0,
      verified_residents: 0,
      unverified_residents: 0,
      total_users: 0,
      total_staf: 0,
    }),
  },
  requestStats: {
    type: Object,
    default: () => ({
      diajukan: 0,
      diproses: 0,
      siap_diambil: 0,
      selesai: 0,
      ditolak: 0,
    }),
  },
  hamletDistribution: {
    type: Array,
    default: () => [],
  },
  recentRequests: {
    type: Array,
    default: () => [],
  },
  hasLetterRequests: {
    type: Boolean,
    default: false,
  },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const periodFilter = ref('today');

// Format current date in Indonesian
const todayFormatted = new Intl.DateTimeFormat('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric',
}).format(new Date());

// Calculate total requests for percentages
const totalTrackedRequests = computed(() => {
  const { diajukan = 0, diproses = 0, siap_diambil = 0, selesai = 0, ditolak = 0 } = props.requestStats;
  return diajukan + diproses + siap_diambil + selesai + ditolak;
});

function calculateStatusPercentage(count) {
  if (!totalTrackedRequests.value) return 0;
  return Math.round((count / totalTrackedRequests.value) * 100);
}
</script>

<template>
  <Head title="Dashboard Admin" />

  <AdminLayout>
    <!-- ==================== HEADER SECTION ==================== -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">
          Selamat datang kembali, <span class="text-teal-600">{{ user?.name || 'Administrator' }}</span>!
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Berikut ringkasan statistik dan operasional pelayanan administrasi Desa Gelam Jaya hari ini.
        </p>
      </div>

      <!-- Right: Date & Filter Badge -->
      <div class="flex items-center gap-2.5 self-start md:self-auto">
      </div>
    </div>

    <!-- ==================== TOP METRICS ROW (5 STAT CARDS) ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <!-- Card 1: Total Pengajuan Hari Ini -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengajuan Hari Ini</span>
          <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
            📄
          </div>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
          <p class="text-3xl font-black text-slate-800">{{ metrics.today_requests }}</p>
          <span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-700">
            Hari ini
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
          Seluruh kanal layanan
        </p>
      </div>

      <!-- Card 2: Pengajuan Daring -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kanal Daring</span>
          <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
            🌐
          </div>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
          <p class="text-3xl font-black text-slate-800">{{ metrics.daring_requests }}</p>
          <span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700">
            Online
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
          Pengajuan mandiri warga
        </p>
      </div>

      <!-- Card 3: Pengajuan Luring -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kanal Luring</span>
          <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
            🏢
          </div>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
          <p class="text-3xl font-black text-slate-800">{{ metrics.luring_requests }}</p>
          <span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
            Loket
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
          Input staf kantor desa
        </p>
      </div>

      <!-- Card 4: Penduduk Terverifikasi -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terverifikasi</span>
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
            ✓
          </div>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
          <p class="text-3xl font-black text-slate-800">{{ metrics.verified_residents }}</p>
          <span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
            Valid
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          Dari {{ metrics.total_residents }} data penduduk
        </p>
      </div>

      <!-- Card 5: Total Penduduk -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-xs hover:shadow-md transition-all group">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Penduduk</span>
          <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-base group-hover:scale-105 transition-transform">
            👥
          </div>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
          <p class="text-3xl font-black text-slate-800">{{ metrics.total_residents }}</p>
          <span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
            Warga
          </span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
          {{ metrics.unverified_residents }} belum verifikasi
        </p>
      </div>
    </div>

    <!-- ==================== MAIN CONTENT ROW (SPLIT GRID) ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Kiri: Ringkasan Statistik Status Pengajuan (Wide Card, col-span-7) -->
      <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div>
            <h2 class="text-base font-bold text-slate-800">Status Pengajuan Surat</h2>
            <p class="text-xs text-slate-400 mt-0.5">Distribusi status seluruh permohonan administrasi surat desa</p>
          </div>
          <Link
            href="/staf/pengajuan"
            class="text-xs font-bold text-teal-600 hover:text-teal-700 transition"
          >
            Antrean
          </Link>
        </div>

        <!-- Visual Multi-segment Progress Bar -->
        <div class="space-y-2">
          <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden flex">
            <div
              class="h-full bg-blue-500 transition-all duration-500"
              :style="{ width: (calculateStatusPercentage(requestStats.diajukan) || (totalTrackedRequests ? 0 : 20)) + '%' }"
              title="Diajukan"
            ></div>
            <div
              class="h-full bg-amber-400 transition-all duration-500"
              :style="{ width: (calculateStatusPercentage(requestStats.diproses) || (totalTrackedRequests ? 0 : 25)) + '%' }"
              title="Diproses"
            ></div>
            <div
              class="h-full bg-teal-500 transition-all duration-500"
              :style="{ width: (calculateStatusPercentage(requestStats.siap_diambil) || (totalTrackedRequests ? 0 : 15)) + '%' }"
              title="Siap Diambil"
            ></div>
            <div
              class="h-full bg-emerald-500 transition-all duration-500"
              :style="{ width: (calculateStatusPercentage(requestStats.selesai) || (totalTrackedRequests ? 0 : 30)) + '%' }"
              title="Selesai"
            ></div>
            <div
              class="h-full bg-rose-500 transition-all duration-500"
              :style="{ width: (calculateStatusPercentage(requestStats.ditolak) || (totalTrackedRequests ? 0 : 10)) + '%' }"
              title="Ditolak"
            ></div>
          </div>

          <div class="flex items-center justify-between text-[11px] text-slate-400">
            <span>Progress Pengajuan Aktif</span>
            <span class="font-semibold text-slate-600">
              {{ totalTrackedRequests }} Total Pengajuan Tercatat
            </span>
          </div>
        </div>

        <!-- Status Breakdown Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
          <!-- Diajukan -->
          <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-100/80">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              <span class="text-xs font-bold text-blue-900">Diajukan</span>
            </div>
            <p class="text-xl font-extrabold text-blue-950 mt-2">{{ requestStats.diajukan }}</p>
            <p class="text-[10px] text-blue-600/90 font-medium mt-0.5">Menunggu respon</p>
          </div>

          <!-- Diproses -->
          <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-100/80">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              <span class="text-xs font-bold text-amber-900">Diproses</span>
            </div>
            <p class="text-xl font-extrabold text-amber-950 mt-2">{{ requestStats.diproses }}</p>
            <p class="text-[10px] text-amber-600/90 font-medium mt-0.5">Sedang dikerjakan</p>
          </div>

          <!-- Siap Diambil -->
          <div class="p-3.5 rounded-xl bg-teal-50/60 border border-teal-100/80">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-teal-500"></span>
              <span class="text-xs font-bold text-teal-900">Siap Ambil</span>
            </div>
            <p class="text-xl font-extrabold text-teal-950 mt-2">{{ requestStats.siap_diambil }}</p>
            <p class="text-[10px] text-teal-600/90 font-medium mt-0.5">Surat dicetak</p>
          </div>

          <!-- Selesai -->
          <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100/80">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span class="text-xs font-bold text-emerald-900">Selesai</span>
            </div>
            <p class="text-xl font-extrabold text-emerald-950 mt-2">{{ requestStats.selesai }}</p>
            <p class="text-[10px] text-emerald-600/90 font-medium mt-0.5">Tuntas diserahkan</p>
          </div>

          <!-- Ditolak -->
          <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-100/80">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-rose-500"></span>
              <span class="text-xs font-bold text-rose-900">Ditolak</span>
            </div>
            <p class="text-xl font-extrabold text-rose-950 mt-2">{{ requestStats.ditolak }}</p>
            <p class="text-[10px] text-rose-600/90 font-medium mt-0.5">Tidak memenuhi syarat</p>
          </div>
        </div>
      </div>

      <!-- Kanan: Distribusi Penduduk per Dusun (Secondary Card, col-span-5) -->
      <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/70 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div>
            <h2 class="text-base font-bold text-slate-800">Distribusi Wilayah Dusun</h2>
            <p class="text-xs text-slate-400 mt-0.5">Persebaran jumlah penduduk di {{ hamletDistribution.length }} dusun</p>
          </div>
          <Link
            href="/staf/penduduk"
            class="text-xs font-bold text-teal-600 hover:text-teal-700 transition"
          >
            Lihat Data
          </Link>
        </div>

        <!-- Dusun List / Bars -->
        <div class="space-y-4">
          <div
            v-for="hamlet in hamletDistribution"
            :key="hamlet.id"
            class="space-y-1.5 group"
          >
            <div class="flex items-center justify-between text-xs">
              <div class="font-bold text-slate-700 flex items-center gap-1.5">
                <span>📍</span>
                <span>{{ hamlet.name }}</span>
                <span v-if="hamlet.head_name" class="font-normal text-slate-400 text-[11px]">
                  (Kepala: {{ hamlet.head_name }})
                </span>
              </div>
              <div class="font-mono font-bold text-slate-800">
                {{ hamlet.count }} <span class="text-slate-400 font-normal">({{ hamlet.percentage }}%)</span>
              </div>
            </div>

            <!-- Progress Bar per Hamlet -->
            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
              <div
                class="h-full bg-gradient-to-r from-teal-500 to-cyan-400 rounded-full transition-all duration-500 group-hover:brightness-105"
                :style="{ width: (hamlet.percentage || 0) + '%' }"
              ></div>
            </div>
          </div>

          <div v-if="hamletDistribution.length === 0" class="py-6 text-center text-xs text-slate-400">
            Belum ada data dusun yang terdaftar.
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== BOTTOM ROW (PENGAJUAN TERBARU) ==================== -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs overflow-hidden">
      <!-- Table Header Bar -->
      <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
        <div>
          <h2 class="text-base font-bold text-slate-800">Pengajuan Surat Terbaru</h2>
          <p class="text-xs text-slate-400 mt-0.5">Daftar permohonan surat masuk paling akhir dari warga</p>
        </div>
        <Link
          href="/staf/pengajuan"
          class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-teal-50 text-slate-700 hover:text-teal-700 border border-slate-200/80 text-xs font-bold transition shadow-2xs"
        >
          Lihat Semua Pengajuan
        </Link>
      </div>

      <!-- Clean Table Layout -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
          <thead class="bg-slate-50/60 text-slate-500 font-semibold text-xs tracking-wider uppercase">
            <tr>
              <th class="px-6 py-3.5">No. Registrasi</th>
              <th class="px-6 py-3.5">Nama Pemohon</th>
              <th class="px-6 py-3.5">Jenis Surat</th>
              <th class="px-6 py-3.5">Kanal</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5 text-right">Tanggal Pengajuan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <!-- Real or sample rows -->
            <tr v-for="req in recentRequests" :key="req.id" class="hover:bg-slate-50/70 transition">
              <td class="px-6 py-4 font-mono text-xs font-bold text-teal-700">
                {{ req.registration_no }}
              </td>
              <td class="px-6 py-4 font-semibold text-slate-900">
                {{ req.resident_name }}
              </td>
              <td class="px-6 py-4 text-xs text-slate-600">
                {{ req.letter_type_name || 'Surat Administrasi' }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1"
                  :class="req.channel === 'daring' ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="req.channel === 'daring' ? 'bg-cyan-500' : 'bg-indigo-500'"></span>
                  {{ req.channel === 'daring' ? 'Daring' : 'Luring' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <StatusBadge :status="req.status" />
              </td>
              <td class="px-6 py-4 text-xs text-slate-500 text-right font-mono">
                {{ req.created_at ? new Date(req.created_at).toLocaleDateString('id-ID') : '-' }}
              </td>
            </tr>

            <!-- Empty State / Phase Preview -->
            <tr v-if="recentRequests.length === 0">
              <td colspan="6" class="px-6 py-12 text-center">
                <div class="max-w-sm mx-auto space-y-2">
                  <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl mx-auto shadow-2xs">
                    📑
                  </div>
                  <h3 class="text-sm font-bold text-slate-800">Belum Ada Pengajuan Surat</h3>
                  <p class="text-xs text-slate-400 leading-relaxed">
                    Modul pengajuan surat daring dan luring akan aktif sepenuhnya pada Fase 5 & 6. Pengajuan baru dari warga akan tampil otomatis di tabel ini.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
