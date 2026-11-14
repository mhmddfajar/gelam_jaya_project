<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppToast from '@/Components/AppToast.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const mobileMenuOpen = ref(false);

const panelRoute = computed(() => {
  if (!user.value) return '/login';
  if (user.value.role === 'admin') return '/admin/dashboard';
  if (user.value.role === 'staf') return '/staf/penduduk';
  return '/warga/profil';
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-teal-500 selection:text-white">
    <!-- Toast Notifications -->
    <AppToast />

    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-2xs transition-all">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Branding -->
        <Link href="/" class="flex items-center gap-3 group" @click="mobileMenuOpen = false">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 text-white flex items-center justify-center font-black text-base shadow-sm shadow-teal-600/20 group-hover:scale-105 transition-transform duration-300">
            GJ
          </div>
          <div>
            <div class="flex items-center gap-1.5">
              <span class="font-extrabold text-base sm:text-lg text-slate-900 tracking-tight group-hover:text-teal-700 transition">
                Desa Gelam Jaya
              </span>
            </div>
            <p class="text-[11px] font-medium text-slate-400 -mt-0.5 hidden xs:block">
              Sistem Pelayanan Administrasi Terpadu
            </p>
          </div>
        </Link>

        <!-- Desktop Navigation Links (md and above) -->
        <nav class="hidden md:flex items-center gap-1.5 lg:gap-2 text-sm font-semibold">
          <Link
            href="/"
            class="px-3 py-2 rounded-xl transition-all duration-200"
            :class="$page.url === '/'
              ? 'text-teal-700 bg-teal-50/80 font-bold shadow-2xs'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
          >
            Beranda
          </Link>
          <Link
            href="/profil"
            class="px-3 py-2 rounded-xl transition-all duration-200"
            :class="$page.url.startsWith('/profil')
              ? 'text-teal-700 bg-teal-50/80 font-bold shadow-2xs'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
          >
            Profil Desa
          </Link>
          <Link
            href="/potensi"
            class="px-3 py-2 rounded-xl transition-all duration-200"
            :class="$page.url.startsWith('/potensi')
              ? 'text-teal-700 bg-teal-50/80 font-bold shadow-2xs'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
          >
            Potensi
          </Link>
          <Link
            href="/sarana"
            class="px-3 py-2 rounded-xl transition-all duration-200"
            :class="$page.url.startsWith('/sarana')
              ? 'text-teal-700 bg-teal-50/80 font-bold shadow-2xs'
              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70'"
          >
            Sarana
          </Link>

          <!-- Desktop Auth Status Buttons -->
          <template v-if="user">
            <div class="flex items-center gap-2 ml-2 pl-2 border-l border-slate-200/80">
              <Link
                :href="panelRoute"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-50 text-teal-800 hover:bg-teal-100/80 border border-teal-200/80 text-xs font-bold transition shadow-2xs"
                title="Buka Panel Layanan"
              >
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                <span class="truncate max-w-[120px]">{{ user.name }}</span>
                <span class="capitalize text-teal-600 font-semibold">({{ user.role }})</span>
              </Link>

              <Link
                href="/logout"
                method="post"
                as="button"
                class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 text-xs font-semibold transition active:scale-[0.98]"
                title="Keluar dari sistem"
              >
                Keluar
              </Link>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center gap-2 ml-2 pl-2 border-l border-slate-200/80">
              <Link
                href="/login"
                class="px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-700 active:scale-[0.98] font-bold text-xs transition-all shadow-xs hover:shadow-md hover:shadow-teal-900/10 flex items-center justify-center"
              >
                Masuk
              </Link>
            </div>
          </template>
        </nav>

        <!-- Mobile Controls (Visible on < md) -->
        <div class="flex items-center gap-2 md:hidden">
          <template v-if="user">
            <Link
              :href="panelRoute"
              class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 text-xs font-bold"
            >
              <span class="w-2 h-2 rounded-full bg-teal-500"></span>
              <span class="truncate max-w-[85px]">{{ user.name }}</span>
            </Link>
          </template>
          <template v-else>
            <Link
              href="/login"
              class="px-3 py-1.5 rounded-xl bg-teal-600 text-white font-bold text-xs shadow-xs active:scale-[0.98]"
            >
              Masuk
            </Link>
          </template>

          <!-- Hamburger Button -->
          <button
            type="button"
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-hidden"
            aria-label="Toggle Menu"
          >
            <!-- Hamburger Icon -->
            <svg v-if="!mobileMenuOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Close Icon -->
            <svg v-else class="w-5 h-5 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Drawer Overlay Menu (< md) -->
      <div v-if="mobileMenuOpen" class="md:hidden">
        <!-- Backdrop Blur Overlay -->
        <div
          class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 animate-fade-in"
          @click="mobileMenuOpen = false"
        ></div>

        <!-- Drawer Floating Menu Card -->
        <div class="fixed inset-x-4 top-20 z-50 bg-white rounded-3xl border border-slate-200/90 shadow-2xl p-5 space-y-4 animate-fade-in-up">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Navigasi Halaman</span>
            <button
              type="button"
              @click="mobileMenuOpen = false"
              class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
              aria-label="Tutup Menu"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <nav class="space-y-1 text-sm font-semibold">
            <Link
              href="/"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition"
              :class="$page.url === '/' ? 'text-teal-700 bg-teal-50 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <span>🏠</span>
              <span>Beranda</span>
            </Link>
            <Link
              href="/profil"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition"
              :class="$page.url.startsWith('/profil') ? 'text-teal-700 bg-teal-50 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <span>📜</span>
              <span>Profil Desa</span>
            </Link>
            <Link
              href="/potensi"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition"
              :class="$page.url.startsWith('/potensi') ? 'text-teal-700 bg-teal-50 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <span>🌾</span>
              <span>Potensi Unggulan</span>
            </Link>
            <Link
              href="/sarana"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition"
              :class="$page.url.startsWith('/sarana') ? 'text-teal-700 bg-teal-50 font-bold' : 'text-slate-700 hover:bg-slate-50'"
            >
              <span>🏢</span>
              <span>Sarana & Prasarana</span>
            </Link>
          </nav>

          <div class="pt-3 border-t border-slate-100">
            <template v-if="user">
              <div class="space-y-2">
                <Link
                  :href="panelRoute"
                  @click="mobileMenuOpen = false"
                  class="w-full py-2.5 px-4 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 font-bold text-xs flex items-center justify-center gap-2 active:scale-[0.98]"
                >
                  <span>Panel {{ user.role.toUpperCase() }}</span>
                </Link>
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  @click="mobileMenuOpen = false"
                  class="w-full py-2 px-4 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs flex items-center justify-center active:scale-[0.98]"
                >
                  Keluar Akun
                </Link>
              </div>
            </template>
            <template v-else>
              <div class="grid grid-cols-2 gap-2">
                <Link
                  href="/login"
                  @click="mobileMenuOpen = false"
                  class="py-2.5 px-4 rounded-xl bg-teal-600 text-white font-bold text-xs text-center shadow-xs active:scale-[0.98]"
                >
                  Masuk
                </Link>
                <Link
                  href="/register"
                  @click="mobileMenuOpen = false"
                  class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs text-center active:scale-[0.98]"
                >
                  Daftar Akun
                </Link>
              </div>
            </template>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Container with Smooth Entrance Animation -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">
      <slot />
    </main>

    <!-- Modern Clean Footer -->
    <footer class="bg-white border-t border-slate-200/80 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center font-black text-sm">
                GJ
              </div>
              <span class="font-extrabold text-slate-900 text-base">Pemerintah Desa Gelam Jaya</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed max-w-md">
              Kecamatan Pasar Kemis, Kabupaten Tangerang, Provinsi Banten 15560.
              Pusat pelayanan administrasi terpadu masyarakat dengan komitmen integritas, kecepatan, dan transparansi.
            </p>
          </div>

          <div class="space-y-2">
            <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Navigasi Publik</p>
            <ul class="text-xs space-y-2 text-slate-500">
              <li><Link href="/" class="hover:text-teal-600 transition">Beranda Utama</Link></li>
              <li><Link href="/profil" class="hover:text-teal-600 transition">Profil & Pemerintahan</Link></li>
              <li><Link href="/potensi" class="hover:text-teal-600 transition">Potensi Ekonomi Desa</Link></li>
              <li><Link href="/sarana" class="hover:text-teal-600 transition">Sarana & Prasarana</Link></li>
            </ul>
          </div>

          <div class="space-y-2">
            <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Akses Mandiri</p>
            <ul class="text-xs space-y-2 text-slate-500">
              <li><Link href="/login" class="hover:text-teal-600 transition">Masuk Portal Layanan</Link></li>
              <li><Link href="/register" class="hover:text-teal-600 transition">Registrasi Akun Warga</Link></li>
              <li><span class="text-slate-400">Jam Layanan: Senin–Jumat 08.00–15.00 WIB</span></li>
            </ul>
          </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
          <p>&copy; {{ new Date().getFullYear() }} Pemerintah Desa Gelam Jaya. Hak Cipta Dilindungi.</p>
          <p class="font-mono text-[11px] text-slate-400">Sistem Informasi & Pelayanan Administrasi Desa v2.0</p>
        </div>
      </div>
    </footer>
  </div>
</template>
