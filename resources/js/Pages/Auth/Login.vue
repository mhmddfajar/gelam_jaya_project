<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormField from '@/Components/FormField.vue';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>

<template>
  <Head title="Masuk ke Sistem" />

  <GuestLayout title="Masuk ke Sistem" subtitle="Masukkan kredensial akun Anda untuk mengakses layanan desa">
    <form @submit.prevent="submit" class="space-y-5">
      <FormField label="Alamat Email" id="email" :error="form.errors.email" required>
        <input
          id="email"
          type="email"
          v-model="form.email"
          autocomplete="username"
          required
          autofocus
          placeholder="Masukkan email anda"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
          :class="{ 'border-rose-300 ring-rose-200': form.errors.email }"
        />
      </FormField>

      <FormField label="Password" id="password" :error="form.errors.password" required>
        <div class="relative">
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            autocomplete="current-password"
            required
            placeholder="••••••••"
            class="w-full pl-3.5 pr-11 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            :class="{ 'border-rose-300 ring-rose-200': form.errors.password }"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden transition-colors"
            :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
            :aria-label="showPassword ? 'Sembunyikan password' : 'Lihat password'"
          >
            <!-- Mata Terbuka (Tampilkan) -->
            <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Mata Tercoret (Sembunyikan) -->
            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
          </button>
        </div>
      </FormField>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <input
            type="checkbox"
            v-model="form.remember"
            class="w-4 h-4 text-teal-600 border-slate-300 rounded-sm focus:ring-teal-500"
          />
          <span class="text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
        </label>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full py-2.5 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm shadow-sm hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
      >
        <span v-if="form.processing">Memproses Masuk...</span>
        <span v-else>Masuk ke Akun</span>
      </button>

      <div class="pt-4 text-center border-t border-slate-100">
        <p class="text-xs text-slate-600">
          Belum memiliki akun warga?
          <Link href="/register" class="font-bold text-teal-600 hover:text-teal-700 transition ml-1">
            Daftar Sekarang
          </Link>
        </p>
      </div>
    </form>
  </GuestLayout>
</template>

