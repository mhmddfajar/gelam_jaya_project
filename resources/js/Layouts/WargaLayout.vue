<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppToast from '@/Components/AppToast.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const sidebarOpen = ref(false);

const todayFormatted = new Intl.DateTimeFormat('id-ID', {
  weekday: 'short',
  day: 'numeric',
  month: 'short',
  year: 'numeric',
}).format(new Date());
</script>

<template>
  <div class="min-h-screen bg-slate-50/70 text-slate-800 flex flex-col md:flex-row font-sans antialiased">
    <AppToast />

    <!-- Mobile Topbar -->
    <div class="md:hidden bg-white border-b border-slate-200/80 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-xs">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
          GJ
        </div>
        <div>
          <span class="font-bold text-sm text-slate-900 tracking-tight block leading-none">Desa Gelam Jaya</span>
          <span class="text-[10px] font-semibold text-emerald-600">Portal Warga</span>
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

    <!-- Sidebar (User-Friendly Clean Soft Light Style) -->
    <aside
      class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 transform transition-transform duration-200 ease-in-out md:translate-x-0 md:static md:inset-auto md:shrink-0 flex flex-col shadow-xs"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <!-- Brand Header -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
            GJ
          </div>
          <div>
            <h1 class="font-bold text-sm text-slate-900 leading-tight tracking-tight">Desa Gelam Jaya</h1>
            <p class="text-[11px] text-emerald-600 font-semibold tracking-wide">Portal Layanan Warga</p>
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
          Layanan Surat
        </div>

        <Link
          href="/warga/pengajuan"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/warga/pengajuan')
            ? 'bg-emerald-50 text-emerald-800 font-bold border-l-4 border-emerald-600 shadow-xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
        >
          <span class="text-base">📑</span>
          <span>Pengajuan Saya</span>
        </Link>

        <div class="pt-4 pb-1 px-3 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
          Data Pribadi
        </div>

        <Link
          href="/warga/profil"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/warga/profil')
            ? 'bg-emerald-50 text-emerald-800 font-bold border-l-4 border-emerald-600 shadow-xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
        >
          <span class="text-base">👤</span>
          <span>Data Diri & KTP</span>
        </Link>

        <Link
          href="/akun/ganti-password"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all"
          :class="$page.url.startsWith('/akun/ganti-password')
            ? 'bg-emerald-50 text-emerald-800 font-bold border-l-4 border-emerald-600 shadow-xs'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
        >
          <span class="text-base">🔑</span>
          <span>Ganti Password</span>
        </Link>
      </nav>

      <!-- Sidebar Footer (Profile & Logout) -->
      <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'W' }}
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

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Topbar Header -->
      <header class="bg-white border-b border-slate-200/80 h-16 px-6 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
        <div class="flex items-center gap-3">
          <h2 class="text-base sm:text-lg font-extrabold text-slate-800 tracking-tight">
            <slot name="header">Portal Layanan Warga</slot>
          </h2>
        </div>

        <div class="flex items-center gap-3">
          <!-- Date Badge -->
          <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs font-semibold text-slate-600">
            <span class="text-emerald-500">📅</span>
            <span>{{ todayFormatted }}</span>
          </div>

          <!-- Status Verifikasi Warga Badge -->
          <span
            v-if="user?.is_verified"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Terverifikasi
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            Belum Verifikasi
          </span>
        </div>
      </header>

      <!-- Main Body Container with Smooth Entrance Animation -->
      <main class="flex-1 p-5 md:p-8 max-w-7xl w-full mx-auto space-y-6 animate-fade-in-up">
        <slot />
      </main>
    </div>
  </div>
</template>
