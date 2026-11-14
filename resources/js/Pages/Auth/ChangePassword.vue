<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StafLayout from '@/Layouts/StafLayout.vue';
import WargaLayout from '@/Layouts/WargaLayout.vue';
import FormField from '@/Components/FormField.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const currentLayout = computed(() => {
  if (user.value?.role === 'admin') return AdminLayout;
  if (user.value?.role === 'staf') return StafLayout;
  return WargaLayout;
});

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.put('/akun/ganti-password', {
    onSuccess: () => form.reset(),
  });
}
</script>

<template>
  <Head title="Ganti Password" />

  <component :is="currentLayout">
    <template #header>Pengaturan Keamanan Akun</template>

    <div class="max-w-xl mx-auto">
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 md:p-8 space-y-6">
        <div>
          <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Perbarui Password</h3>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Gunakan kombinasi password yang kuat dengan minimal 8 karakter demi keamanan akun Anda.
          </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <FormField label="Password Saat Ini" id="current_password" :error="form.errors.current_password" required>
            <div class="relative">
              <input
                id="current_password"
                :type="showCurrentPassword ? 'text' : 'password'"
                v-model="form.current_password"
                autocomplete="current-password"
                required
                class="w-full pl-3.5 pr-11 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
                :class="{ 'border-rose-300 ring-rose-200': form.errors.current_password }"
              />
              <button
                type="button"
                @click="showCurrentPassword = !showCurrentPassword"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden transition-colors"
                :title="showCurrentPassword ? 'Sembunyikan password' : 'Lihat password'"
                :aria-label="showCurrentPassword ? 'Sembunyikan password' : 'Lihat password'"
              >
                <svg v-if="!showCurrentPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
          </FormField>

          <FormField label="Password Baru" id="password" :error="form.errors.password" required help="Minimal 8 karakter">
            <div class="relative">
              <input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                autocomplete="new-password"
                required
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
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
          </FormField>

          <FormField label="Konfirmasi Password Baru" id="password_confirmation" :error="form.errors.password_confirmation" required>
            <div class="relative">
              <input
                id="password_confirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                required
                class="w-full pl-3.5 pr-11 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
                :class="{ 'border-rose-300 ring-rose-200': form.errors.password_confirmation }"
              />
              <button
                type="button"
                @click="showPasswordConfirmation = !showPasswordConfirmation"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden transition-colors"
                :title="showPasswordConfirmation ? 'Sembunyikan password' : 'Lihat password'"
                :aria-label="showPasswordConfirmation ? 'Sembunyikan password' : 'Lihat password'"
              >
                <svg v-if="!showPasswordConfirmation" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
          </FormField>

          <div class="pt-4 flex justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all disabled:opacity-50"
            >
              <span v-if="form.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan Password</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </component>
</template>

