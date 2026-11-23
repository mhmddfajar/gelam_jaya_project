<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppToast from '@/Components/AppToast.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const sidebarOpen = ref(false);

const todayFormatted = new Intl.DateTimeFormat('id-ID', {
  weekday: 'long',
  day: 'numeric',
  month: 'long',
  year: 'numeric',
}).format(new Date());
</script>

<template>
  <div class="min-h-screen bg-slate-50/70 text-slate-800 flex flex-col md:flex-row font-sans antialiased">
    <AppToast />

    <!-- Mobile Topbar -->
    <div class="md:hidden bg-white border-b border-slate-200/80 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-xs">
      <div class="flex items-center gap-2.5">
        <img
          src="/images/logo.webp"
          alt="Logo Desa Gelam Jaya"
          class="w-9 h-9 object-contain"
        />
        <div>
          <span class="font-bold text-sm text-slate-900 tracking-tight block leading-none">Desa Gelam Jaya</span>
          <span class="text-[10px] font-semibold text-teal-600">Admin Panel</span>
        </div>
      </div>
      <button
        type="button"
        @click="sidebarOpen = !sidebarOpen"
        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>

    <!-- Sidebar (Clean Soft Light / Modern SaaS Style) -->
    <aside
      class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 transform transition-transform duration-200 ease-in-out md:translate-x-0 md:sticky md:top-0 md:h-screen md:self-start md:shrink-0 md:inset-auto flex flex-col shadow-xs"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <!-- Brand Header -->
      <div class="h-16 shrink-0 flex items-center justify-between px-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <img
            src="/images/logo.webp"
            alt="Logo Desa Gelam Jaya"
            class="w-9 h-9 object-contain"
          />
          <div>
            <h1 class="font-bold text-sm text-slate-900 leading-tight tracking-tight">Desa Gelam Jaya</h1>
            <p class="text-[11px] text-teal-600 font-semibold tracking-wide">Panel Administrator</p>
          </div>
        </div>

        <!-- Mobile Close Button X -->
        <button
          type="button"
          @click="sidebarOpen = false"
          class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
          aria-label="Tutup Sidebar"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto text-sm font-medium">
        <div class="px-3 pb-1 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
          Menu Utama
        </div>

        <Link
          href="/admin/dashboard"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/admin/dashboard')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">📊</span>
          <span>Dashboard</span>
        </Link>

        <Link
          href="/admin/pengguna"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/admin/pengguna')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">👥</span>
          <span>Kelola Pengguna</span>
        </Link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
          Layanan & Warga
        </div>

        <Link
          href="/staf/pengajuan"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/staf/pengajuan')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">📄</span>
          <span>Antrean Pengajuan</span>
        </Link>

        <Link
          href="/staf/penduduk"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/staf/penduduk')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">📋</span>
          <span>Data Penduduk</span>
        </Link>

        <Link
          href="/admin/jenis-surat"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/admin/jenis-surat')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Jenis Surat & Syarat</span>
        </Link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
          Info Desa
        </div>

        <Link
          href="/admin/desa/identitas-wilayah"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/admin/desa/identitas-wilayah')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">🏛️</span>
          <span>Identitas & Wilayah</span>
        </Link>

        <Link
          href="/admin/desa/pemerintahan"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/admin/desa/pemerintahan') || $page.url.startsWith('/admin/desa/visi-misi')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">📜</span>
          <span>Visi Misi & Perangkat</span>
        </Link>

        <Link
          href="/staf/desa/potensi"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/staf/desa/potensi')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">🌾</span>
          <span>Potensi Desa</span>
        </Link>

        <Link
          href="/staf/desa/sarana"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/staf/desa/sarana')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">🏢</span>
          <span>Sarana & Prasarana</span>
        </Link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
          Pengaturan Akun
        </div>

        <Link
          href="/akun/ganti-password"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/akun/ganti-password')
            ? 'bg-teal-50 text-teal-800 font-semibold border border-teal-200/70 shadow-2xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent'"
        >
          <span class="text-base">🔑</span>
          <span>Ganti Password</span>
        </Link>
      </nav>

      <!-- Sidebar Footer (Profile & Logout) -->
      <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3 shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-teal-500 to-cyan-400 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'A' }}
          </div>
          <div class="truncate">
            <p class="text-xs font-bold text-slate-800 truncate">{{ user?.name }}</p>
            <p class="text-[11px] text-slate-400 truncate">{{ user?.email }}</p>
          </div>
        </div>

        <Link
          href="/logout"
          method="post"
          as="button"
          class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition border border-transparent hover:border-rose-100 shrink-0"
          title="Keluar dari akun"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </Link>
      </div>
    </aside>

    <!-- Overlay backdrop for mobile -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 md:hidden animate-fade-in"
    ></div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Modern SaaS Top Header -->
      <header class="bg-white border-b border-slate-200/80 h-16 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
        <!-- Left: Panel Indicator -->
        <div class="flex items-center gap-3">
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-semibold text-slate-600">
            <span class="text-teal-600 font-bold">🏛️</span>
            <span>Panel Administrasi Desa Gelam Jaya</span>
          </span>
        </div>

        <!-- Right: Actions & User Info -->
        <div class="flex items-center gap-3">
          <!-- Date Badge -->
          <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs font-semibold text-slate-600">
            <span class="text-teal-600">📅</span>
            <span>{{ todayFormatted }}</span>
          </div>

          <!-- Status / Role Badge -->
          <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200/70 shadow-2xs">
              <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
              Administrator
            </span>
          </div>
        </div>
      </header>

      <!-- Page Body with Smooth Entrance Animation -->
      <main class="flex-1 p-5 md:p-8 max-w-7xl w-full mx-auto space-y-6 animate-fade-in-up">
        <slot />
      </main>
    </div>
  </div>
</template>
