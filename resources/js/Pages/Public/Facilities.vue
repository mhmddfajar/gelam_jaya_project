<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

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
  selectedCategory: {
    type: String,
    default: '',
  },
});

function selectCategory(categoryKey) {
  router.get('/sarana', {
    category: categoryKey || undefined,
  }, { preserveState: true });
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

function getCategoryIcon(category) {
  switch (category) {
    case 'pendidikan':
      return 'school';
    case 'kesehatan':
      return 'health';
    case 'fasilitas_umum':
      return 'government';
    case 'ibadah':
      return 'worship';
    case 'olahraga':
      return 'sports';
    case 'jalan_transportasi':
      return 'transport';
    default:
      return 'building';
  }
}

function cleanPaginationLabel(label) {
  if (!label) return '';
  const str = String(label).toLowerCase();
  if (
    str.includes('previous') ||
    str.includes('&laquo;') ||
    str.includes('«') ||
    str.includes('sebelumnya')
  ) {
    return '&lt;';
  }
  if (
    str.includes('next') ||
    str.includes('&raquo;') ||
    str.includes('»') ||
    str.includes('berikutnya')
  ) {
    return '&gt;';
  }
  return label;
}
</script>

<template>
  <Head title="Sarana & Prasarana Publik Desa Gelam Jaya" />

  <AppLayout>
    <div class="space-y-10 py-2 sm:py-6 max-w-6xl mx-auto">
      <!-- Header Banner Konsisten (SaaS Modern, Breadcrumb, Gradient Title) -->
      <section class="relative overflow-hidden rounded-3xl py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center">
        <!-- Ambient Background Glows -->
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
            Sarana & Prasarana
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
              Publik Desa
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Pusat informasi inventaris fasilitas pendidikan, layanan posyandu kesehatan masyarakat, gedung pemerintahan, dan sarana umum di wilayah Desa Gelam Jaya.
          </p>
        </div>
      </section>

      <!-- Category Filter Pills -->
      <div class="flex flex-wrap items-center justify-center gap-2">
        <button
          type="button"
          @click="selectCategory('')"
          class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs duration-200"
          :class="!selectedCategory
            ? 'bg-teal-600 text-white shadow-xs scale-105'
            : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
        >
          Semua Sarana ({{ facilities.total || facilities.data.length }})
        </button>
        <button
          v-for="(label, key) in categories"
          :key="key"
          type="button"
          @click="selectCategory(key)"
          class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs duration-200"
          :class="selectedCategory === key
            ? 'bg-teal-600 text-white shadow-xs scale-105'
            : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
        >
          {{ label }}
        </button>
      </div>

      <!-- Facilities Grid (Modern SaaS Cards with Thematic Icons) -->
      <div v-if="facilities.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in facilities.data"
          :key="item.id"
          class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 flex flex-col justify-between space-y-4 group"
        >
          <div class="space-y-3.5">
            <!-- Header Badges & Thematic SVG Icon -->
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform duration-300">
                  <svg v-if="getCategoryIcon(item.category) === 'school'" class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                  </svg>
                  <svg v-else-if="getCategoryIcon(item.category) === 'health'" class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  <svg v-else-if="getCategoryIcon(item.category) === 'government'" class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                  </svg>
                  <svg v-else class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>

                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200/70">
                  {{ categories[item.category] || item.category }}
                </span>
              </div>

              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                :class="getConditionBadgeClass(item.condition)"
              >
                {{ conditions[item.condition] || item.condition }}
              </span>
            </div>

            <!-- Title & Description -->
            <div>
              <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-teal-700 transition">
                {{ item.name }}
              </h3>

              <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                {{ item.description || 'Fasilitas umum pelayanan warga Desa Gelam Jaya.' }}
              </p>
            </div>
          </div>

          <!-- Footer Metadata -->
          <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span class="flex items-center gap-1.5 truncate max-w-[200px]">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="truncate">{{ item.location || 'Wilayah Desa Gelam Jaya' }}</span>
            </span>

            <span class="font-bold text-slate-700 font-mono text-xs bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/60">
              {{ item.quantity }} Unit
            </span>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-3xl border border-slate-200/70 p-12 text-center max-w-md mx-auto space-y-4 shadow-xs">
        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-3xl mx-auto shadow-2xs">
          🏢
        </div>
        <h3 class="text-base font-bold text-slate-900">Belum Ada Data Sarana</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Tidak ditemukan data sarana dan prasarana untuk kategori yang dipilih saat ini.
        </p>
        <button
          v-if="selectedCategory"
          @click="selectCategory('')"
          class="inline-block px-4 py-2 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-xs transition"
        >
          Tampilkan Semua Sarana
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="facilities.links && facilities.links.length > 3" class="flex justify-center items-center gap-1.5 pt-6">
        <template v-for="(link, index) in facilities.links" :key="index">
          <Link
            v-if="link.url"
            :href="link.url"
            class="min-w-9 h-9 px-3 rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center justify-center"
            :class="link.active ? 'bg-teal-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300'"
            v-html="cleanPaginationLabel(link.label)"
          />
          <span
            v-else
            class="min-w-9 h-9 px-3 rounded-xl text-xs text-slate-300 select-none flex items-center justify-center font-bold"
            v-html="cleanPaginationLabel(link.label)"
          />
        </template>
      </div>
    </div>
  </AppLayout>
</template>
