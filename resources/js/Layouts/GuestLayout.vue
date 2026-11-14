<script setup>
import { Link } from '@inertiajs/vue3';
import AppToast from '@/Components/AppToast.vue';

defineProps({
  title: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Subtle gradient background -->
    <div class="absolute inset-0 bg-gradient-to-b from-teal-50/30 via-white to-slate-50 pointer-events-none"></div>

    <AppToast />

    <div class="relative sm:mx-auto sm:w-full sm:max-w-md text-center">
      <Link href="/" class="inline-flex items-center gap-3 group">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-teal-700/20 group-hover:scale-105 transition-transform">
          GJ
        </div>
      </Link>
      <h2 class="mt-4 text-2xl font-bold text-slate-900 tracking-tight">
        {{ title || 'Desa Gelam Jaya' }}
      </h2>
      <p v-if="subtitle" class="mt-1.5 text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
        {{ subtitle }}
      </p>
    </div>

    <div class="relative mt-8 sm:mx-auto sm:w-full sm:max-w-xl animate-fade-in-up">
      <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 border border-slate-200/80 rounded-3xl">
        <slot />
      </div>

      <div class="mt-6 text-center text-xs text-slate-400 space-y-2">
        <div>
          <Link href="/" class="font-medium text-slate-600 hover:text-teal-600 transition-colors">
            Kembali ke Beranda
          </Link>
        </div>
        <p>&copy; {{ new Date().getFullYear() }} Pemerintah Desa Gelam Jaya</p>
      </div>
    </div>
  </div>
</template>

