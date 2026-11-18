<script setup>
import { onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  profile: {
    type: Object,
    required: true,
  },
  officials: {
    type: Array,
    default: () => [],
  },
  hamlets: {
    type: Array,
    default: () => [],
  },
});

let scrollObserver = null;

onMounted(() => {
  const elements = document.querySelectorAll('.reveal-on-scroll');
  if ('IntersectionObserver' in window) {
    scrollObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal-visible');
            obs.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px',
      }
    );

    elements.forEach((el) => scrollObserver.observe(el));
  } else {
    elements.forEach((el) => el.classList.add('reveal-visible'));
  }
});

onUnmounted(() => {
  if (scrollObserver) {
    scrollObserver.disconnect();
  }
});

const hamletDataConfig = {
  'Dusun I': { head: 'Janawi', rw: 'RW 01 s/d RW 05', rt: '28 RT' },
  'Dusun II': { head: 'Askol', rw: 'RW 06 s/d RW 10', rt: '54 RT' },
  'Dusun III': { head: 'Roni Lesmana', rw: 'RW 11 s/d RW 16', rt: '44 RT' },
  'Dusun IV': { head: 'Juhri', rw: 'RW 17 s/d RW 23', rt: '43 RT' },
};

function getHamletKey(hamlet) {
  if (hamlet?.name && hamletDataConfig[hamlet.name]) return hamlet.name;
  if (hamlet?.id === 1) return 'Dusun I';
  if (hamlet?.id === 2) return 'Dusun II';
  if (hamlet?.id === 3) return 'Dusun III';
  if (hamlet?.id === 4) return 'Dusun IV';
  return 'Dusun I';
}

function getHamletHead(hamlet) {
  const key = getHamletKey(hamlet);
  return hamlet?.head_name || hamletDataConfig[key]?.head || 'Janawi';
}

function getHamletDetails(hamlet) {
  const key = getHamletKey(hamlet);
  return hamletDataConfig[key] || { rw: 'RW 01 s/d RW 05', rt: '28 RT' };
}
</script>

<template>
  <Head title="Profil & Tata Kelola Desa Gelam Jaya" />

  <AppLayout>
    <div class="space-y-12 sm:space-y-16 py-2 sm:py-6 max-w-6xl mx-auto">
      <!-- Header Banner Konsisten (SaaS Modern, Gradient Title, Ambient Slow Float) -->
      <section class="relative overflow-hidden rounded-3xl py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-b from-teal-50/50 via-white to-slate-50 border border-teal-100/70 shadow-xs text-center animate-fade-in-up">
        <!-- Ambient Background Glows with Slow Float Physics -->
        <div class="absolute -top-24 -left-24 w-80 sm:w-96 h-80 sm:h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute -bottom-24 -right-24 w-80 sm:w-96 h-80 sm:h-96 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none animate-float-slow-delayed"></div>

        <div class="relative z-10 max-w-3xl mx-auto space-y-4">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
            Profil & Pemerintahan
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
              Desa Gelam Jaya
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Mengenal lebih dekat identitas kewilayahan, statistik demografi kependudukan, visi misi pembangunan, serta susunan aparatur pemerintah Desa Gelam Jaya.
          </p>
        </div>
      </section>

      <!-- 1. Sejarah Desa Gelam Jaya (Asal Usul & Narasi Riil Pemekaran) -->
      <section class="reveal-on-scroll reveal-hidden bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-6 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center gap-3.5 border-b border-slate-100 pb-5">
          <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 shadow-2xs">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
          </div>
          <div>
            <span class="text-[11px] font-bold text-teal-600 uppercase tracking-wider block">Asal Usul & Kilas Balik</span>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
              Sejarah Desa Gelam Jaya
            </h2>
          </div>
        </div>

        <div class="bg-gradient-to-br from-teal-50/40 via-slate-50/60 to-white rounded-2xl p-6 sm:p-8 border border-teal-100/70 relative overflow-hidden">
          <div class="relative z-10 space-y-4">
            <p class="text-sm sm:text-base text-slate-700 leading-relaxed font-normal">
              Desa Gelam Jaya merupakan hasil pemekaran wilayah dari Kelurahan Kuta Jaya di Kecamatan Pasar Kemis, Kabupaten Tangerang, Provinsi Banten. Seiring perkembangan Kecamatan Pasar Kemis yang berawal dari Kemantren Pasarkemis (dimekarkan dari Kecamatan Rajeg pada tahun 1948 dan menjadi kecamatan definitif pada tahun 1950), Desa Gelam Jaya tumbuh pesat menjadi kawasan pemukiman dan daerah penyangga industri yang mandiri, sejahtera, serta berorientasi pada pelayanan masyarakat.
            </p>

            <div class="pt-4 border-t border-teal-100/80 flex flex-wrap items-center gap-4 sm:gap-8 text-xs text-slate-500">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Wilayah Asal: <strong class="text-slate-800">Kelurahan Kuta Jaya</strong></span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Kecamatan Induk: <strong class="text-slate-800">Pasar Kemis</strong></span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Status: <strong class="text-slate-800">Penyangga Pemukiman & Industri</strong></span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. Statistik Demografi Kependudukan Riil (Responsive: 2 Kolom di Mobile, 4 di Desktop) -->
      <section class="reveal-on-scroll reveal-hidden space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
              <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>Statistik Demografi & Kependudukan</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Pangkalan data resmi kependudukan dan keluarga Desa Gelam Jaya.</p>
          </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          <!-- Total Penduduk -->
          <div
            class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-teal-900/5 hover:border-teal-200/80 group"
            style="transition-delay: 50ms;"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penduduk</span>
              <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-2xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
            <p class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight">
              {{ Number(profile.total_population || 40030).toLocaleString('id-ID') }}
            </p>
            <p class="text-[11px] text-teal-600 font-semibold mt-1">Jiwa Terdaftar</p>
          </div>

          <!-- Total KK -->
          <div
            class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-cyan-900/5 hover:border-cyan-200/80 group"
            style="transition-delay: 100ms;"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Kepala Keluarga</span>
              <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-2xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
              </div>
            </div>
            <p class="text-xl sm:text-3xl font-black text-cyan-600 tracking-tight">
              {{ Number(profile.total_households || 11015).toLocaleString('id-ID') }}
            </p>
            <p class="text-[11px] text-cyan-600 font-semibold mt-1">Kepala Keluarga (KK)</p>
          </div>

          <!-- Penduduk Laki-laki -->
          <div
            class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-sky-900/5 hover:border-sky-200/80 group"
            style="transition-delay: 150ms;"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Laki-Laki</span>
              <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xs group-hover:scale-110 transition-transform duration-300 shadow-2xs">
                ♂
              </div>
            </div>
            <p class="text-xl sm:text-3xl font-black text-sky-600 tracking-tight">
              {{ Number(profile.male_population || 20218).toLocaleString('id-ID') }}
            </p>
            <p class="text-[11px] text-sky-600 font-semibold mt-1">50.5% Populasi</p>
          </div>

          <!-- Penduduk Perempuan -->
          <div
            class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-pink-900/5 hover:border-pink-200/80 group"
            style="transition-delay: 200ms;"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Perempuan</span>
              <div class="w-8 h-8 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-xs group-hover:scale-110 transition-transform duration-300 shadow-2xs">
                ♀
              </div>
            </div>
            <p class="text-xl sm:text-3xl font-black text-pink-600 tracking-tight">
              {{ Number(profile.female_population || 19812).toLocaleString('id-ID') }}
            </p>
            <p class="text-[11px] text-pink-600 font-semibold mt-1">49.5% Populasi</p>
          </div>
        </div>
      </section>

      <!-- 3. Peta & Letak Geografis Desa (Google Maps Embed & Batas Wilayah) -->
      <section class="reveal-on-scroll reveal-hidden bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-6 transition-all duration-300 hover:shadow-md">
        <div class="border-b border-slate-100 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
              <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <span>Peta & Letak Geografis Desa</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
              Peta digital lokasi Kantor Desa Gelam Jaya, batas administratif, dan data kewilayahan.
            </p>
          </div>
          <a
            href="https://maps.google.com/?q=Kantor+Desa+Gelam+Jaya+Pasar+Kemis+Tangerang"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200/80 text-xs font-bold transition shadow-2xs shrink-0 self-start sm:self-auto active:scale-[0.98]"
          >
            <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Buka di Google Maps</span>
          </a>
        </div>

        <!-- Grid Layout: Peta Embed & Info Geografis -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
          <!-- Kolom Peta Embed (7 Kolom) -->
          <div class="lg:col-span-7 rounded-2xl overflow-hidden border border-slate-200/80 shadow-2xs min-h-[320px] sm:min-h-[400px] bg-slate-100 relative">
            <iframe
              src="https://maps.google.com/maps?q=Kantor+Desa+Gelam+Jaya+Pasar+Kemis+Tangerang&t=&z=16&ie=UTF8&iwloc=&output=embed"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Peta Kantor Desa Gelam Jaya"
              class="w-full h-full min-h-[320px] sm:min-h-[400px]"
            ></iframe>
          </div>

          <!-- Kolom Identitas & Batas Wilayah (5 Kolom) -->
          <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
            <!-- Luas & Kode Wilayah -->
            <div class="grid grid-cols-2 gap-3 text-xs">
              <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60">
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Luas Wilayah</p>
                <p class="font-black text-teal-600 mt-1 text-sm sm:text-base">{{ profile.area_size_ha ? `${profile.area_size_ha} Hektar` : '294.00 Hektar' }}</p>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60">
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Kode Wilayah</p>
                <p class="font-black text-slate-800 mt-1 font-mono text-sm sm:text-base">{{ profile.village_code || '36.03.11.2005' }}</p>
              </div>
            </div>

            <!-- Batas Wilayah 4 Penjuru (Data Riil) -->
            <div class="space-y-2">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Batas Wilayah Administratif</p>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-3 rounded-xl bg-teal-50/60 border border-teal-100/80">
                  <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider block">Batas Utara</span>
                  <span class="font-extrabold text-slate-900 mt-0.5 block truncate">{{ profile.boundary_north || 'Kel. Kutabumi' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-cyan-50/60 border border-cyan-100/80">
                  <span class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider block">Batas Selatan</span>
                  <span class="font-extrabold text-slate-900 mt-0.5 block truncate">{{ profile.boundary_south || 'Kel. Alamjaya' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-sky-50/60 border border-sky-100/80">
                  <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider block">Batas Timur</span>
                  <span class="font-extrabold text-slate-900 mt-0.5 block truncate">{{ profile.boundary_east || 'Kel. Priuk' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-100/80">
                  <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Batas Barat</span>
                  <span class="font-extrabold text-slate-900 mt-0.5 block truncate">{{ profile.boundary_west || 'Kel. Kutajaya' }}</span>
                </div>
              </div>
            </div>

            <!-- Alamat Kantor -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs space-y-1">
              <p class="font-bold text-slate-700 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Lokasi Kantor Pelayanan Desa</span>
              </p>
              <p class="text-slate-500 leading-relaxed text-[11px]">
                {{ profile.address || 'Jl. Villa Regency Tangerang II No. 01 Desa Gelam Jaya, Kec. Pasar Kemis, Kab. Tangerang, Banten 15560' }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Visi & Misi Pembangunan (Dark Premium Card) -->
      <section class="reveal-on-scroll reveal-hidden relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-900 to-teal-950 text-white rounded-3xl p-6 sm:p-12 shadow-xl space-y-8 border border-slate-800">
        <!-- Giant Decorative Quote Watermark -->
        <div class="absolute -right-6 -bottom-10 text-white/[0.04] text-[180px] font-serif font-black select-none pointer-events-none">
          “
        </div>

        <div class="relative z-10 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-400/30 text-teal-300 text-xs font-bold uppercase tracking-wider">
            <svg class="w-3.5 h-3.5 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Visi dan Misi Pembangunan</span>
          </div>

          <div class="space-y-3">
            <p class="text-xs font-bold text-teal-400 uppercase tracking-wider">Visi Desa Gelam Jaya</p>
            <blockquote class="text-xl sm:text-2xl font-bold leading-relaxed text-teal-50 italic">
              "{{ profile.vision || 'Terwujudnya Desa Gelam Jaya yang Maju, Mandiri, Sejahtera, dan Berakhlak Mulia berbasis Pelayanan Administrasi Digital Terpadu.' }}"
            </blockquote>
          </div>

          <div class="pt-6 border-t border-slate-800 space-y-3">
            <p class="text-xs font-bold text-teal-400 uppercase tracking-wider">Misi Pembangunan</p>
            <div class="text-xs sm:text-sm text-slate-300 leading-relaxed whitespace-pre-line bg-slate-800/60 p-6 rounded-2xl border border-slate-700/80">
              {{ profile.mission || "1. Meningkatkan kualitas pelayanan publik berbasis teknologi informasi yang cepat, transparan, dan akuntabel.\n2. Mewujudkan tata kelola pemerintahan desa yang bersih, berintegritas, dan partisipatif.\n3. Mengembangkan potensi ekonomi desa melalui kemitraan UMKM, pertanian modern, dan perdagangan lokal.\n4. Meningkatkan sarana dan prasarana umum, pendidikan, dan kesehatan masyarakat desa yang inklusif." }}
            </div>
          </div>
        </div>
      </section>

      <!-- 5. Pembagian Wilayah Administrasi Dusun (Staggered Reveal) -->
      <section class="reveal-on-scroll reveal-hidden bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-6 transition-all duration-300 hover:shadow-md">
        <div class="border-b border-slate-100 pb-5">
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
            <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Pembagian Wilayah Administrasi Dusun</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1">Struktur pembagian wilayah administratif dusun, kepemimpinan kepala dusun, dan cakupan rukun warga.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <div
            v-for="(hamlet, index) in hamlets"
            :key="hamlet.id"
            class="reveal-on-scroll reveal-hidden p-6 rounded-2xl border border-slate-200/70 bg-gradient-to-b from-slate-50/60 to-white shadow-2xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-teal-900/5 hover:border-teal-200/80 flex flex-col justify-between group"
            :style="{ transitionDelay: `${index * 80}ms` }"
          >
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-base font-bold shadow-2xs group-hover:scale-105 transition-transform duration-300">
                  <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                  Aktif
                </span>
              </div>

              <h3 class="font-extrabold text-slate-900 text-lg group-hover:text-teal-700 transition">
                {{ hamlet.name }}
              </h3>

              <div class="mt-2.5 space-y-1">
                <p class="text-xs text-slate-400 font-medium">Kepala Dusun:</p>
                <p class="text-sm font-bold text-slate-800">{{ getHamletHead(hamlet) }}</p>
              </div>
            </div>

            <div class="mt-5 pt-3.5 border-t border-slate-100 space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Cakupan RW:</span>
                <span class="font-bold text-slate-700 font-mono text-[11px] bg-slate-50 px-2 py-0.5 rounded border border-slate-200/60">
                  {{ getHamletDetails(hamlet).rw }}
                </span>
              </div>
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Total RT:</span>
                <span class="font-extrabold text-teal-600 font-mono text-xs">
                  {{ getHamletDetails(hamlet).rt }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 6. Struktur 12 Aparatur Pemerintah Desa Riil (Responsive 2 Kolom di Mobile, 4 di Desktop) -->
      <section class="reveal-on-scroll reveal-hidden bg-white rounded-3xl border border-slate-200/70 p-6 sm:p-10 shadow-xs space-y-6 transition-all duration-300 hover:shadow-md">
        <div class="border-b border-slate-100 pb-5">
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
            <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Aparatur Pemerintah Desa Gelam Jaya</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1">Susunan aparatur pemerintah desa periode 2021 – 2027 yang siap mengabdi untuk masyarakat.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
          <div
            v-for="(official, index) in officials"
            :key="official.id"
            class="reveal-on-scroll reveal-hidden bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/70 shadow-xs transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-teal-900/5 hover:border-teal-200/80 text-center flex flex-col items-center group"
            :style="{ transitionDelay: `${index * 50}ms` }"
          >
            <!-- Modern Avatar Badge with Initial or Photo -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-50 border-2 border-teal-100/90 overflow-hidden mb-3.5 flex items-center justify-center text-lg sm:text-xl font-black text-teal-700 shadow-2xs group-hover:scale-105 group-hover:border-teal-300 transition-all duration-300">
              <img v-if="official.photo_path" :src="official.photo_path" :alt="official.name" class="w-full h-full object-cover" loading="lazy" />
              <span v-else class="tracking-wider">{{ official.name.charAt(0) }}</span>
            </div>

            <h3 class="font-extrabold text-slate-900 text-xs sm:text-sm leading-snug group-hover:text-teal-700 transition">
              {{ official.name }}
            </h3>

            <p class="text-[11px] sm:text-xs text-teal-600 font-bold mt-1">
              {{ official.position }}
            </p>

            <div class="mt-4 pt-3 border-t border-slate-100 w-full flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400">
              <span>Masa Bakti</span>
              <span class="font-mono font-medium text-slate-600">
                {{ official.period_start || '2021' }} – {{ official.period_end || '2027' }}
              </span>
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
