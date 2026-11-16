<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  potentials: {
    type: Object,
    required: true,
  },
  categories: {
    type: Object,
    default: () => ({}),
  },
  selectedCategory: {
    type: String,
    default: '',
  },
});

function selectCategory(categoryKey) {
  router.get('/potensi', {
    category: categoryKey || undefined,
  }, { preserveState: true });
}

function getPotentialIcon(category) {
  switch (category) {
    case 'pertanian':
      return 'sprout';
    case 'umkm':
      return 'store';
    case 'perikanan':
      return 'fish';
    case 'peternakan':
      return 'barn';
    case 'wisata':
      return 'compass';
    default:
      return 'sparkles';
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
  <Head title="Potensi Ekonomi & Komoditas Desa Gelam Jaya" />

  <AppLayout>
    <div class="space-y-10 py-2 sm:py-6 max-w-6xl mx-auto">
      <!-- Header Banner Konsisten (SaaS Modern, Breadcrumb, Gradient Title) -->
      <section class="relative overflow-hidden rounded-3xl py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center">
        <!-- Ambient Background Glows -->
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
            Potensi Ekonomi &
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
              Komoditas Unggulan
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Eksplorasi ragam komoditas UMKM kerajinan, budidaya perikanan modern, pertanian sayuran hidroponik, dan ruang terbuka hijau yang dikembangkan masyarakat Desa Gelam Jaya.
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
          Semua Sektor ({{ potentials.total || potentials.data.length }})
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

      <!-- Potentials Grid (Modern SaaS Cards, No Broken Images) -->
      <div v-if="potentials.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in potentials.data"
          :key="item.id"
          class="bg-white rounded-2xl border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300/80 overflow-hidden flex flex-col group"
        >
          <!-- Card Image or Thematic SVG Gradient Card Header -->
          <div class="h-48 overflow-hidden relative">
            <img
              v-if="item.photo_path"
              :src="item.photo_path"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-teal-50/70 via-slate-50 to-emerald-50/70 flex items-center justify-center p-6 border-b border-slate-100"
            >
              <!-- SVG Icon Theme by Category -->
              <div class="w-16 h-16 rounded-2xl bg-white border border-teal-100/90 shadow-2xs flex items-center justify-center text-teal-600 group-hover:scale-110 transition-transform duration-300">
                <svg v-if="getPotentialIcon(item.category) === 'sprout'" class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V6m0 0l-4 4m4-4l4 4m-8 9a4 4 0 008 0" />
                </svg>
                <svg v-else-if="getPotentialIcon(item.category) === 'store'" class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <svg v-else-if="getPotentialIcon(item.category) === 'fish'" class="w-8 h-8 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <svg v-else-if="getPotentialIcon(item.category) === 'barn'" class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <svg v-else class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
            </div>

            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-white/95 text-teal-800 shadow-2xs border border-slate-200/60 backdrop-blur-xs uppercase tracking-wider">
              {{ categories[item.category] || item.category }}
            </span>
          </div>

          <!-- Card Content -->
          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-teal-700 transition">
                {{ item.title }}
              </h3>
              <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                {{ item.description }}
              </p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ item.location || 'Desa Gelam Jaya' }}</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-3xl border border-slate-200/70 p-12 text-center max-w-md mx-auto space-y-4 shadow-xs">
        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-3xl mx-auto shadow-2xs">
          🌾
        </div>
        <h3 class="text-base font-bold text-slate-900">Belum Ada Data Potensi</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Tidak ditemukan data potensi desa untuk kategori yang dipilih saat ini.
        </p>
        <button
          v-if="selectedCategory"
          @click="selectCategory('')"
          class="inline-block px-4 py-2 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-xs transition"
        >
          Tampilkan Semua Kategori
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="potentials.links && potentials.links.length > 3" class="flex justify-center items-center gap-1.5 pt-6">
        <template v-for="(link, index) in potentials.links" :key="index">
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
