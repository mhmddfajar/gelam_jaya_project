<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
  hamlets: {
    type: Array,
    default: () => [],
  },
});

const step = ref(1);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
  // Step 1: Akun
  email: '',
  password: '',
  password_confirmation: '',

  // Step 2: Data Diri KTP
  nik: '',
  name: '',
  birth_place: '',
  birth_date: '',
  gender: '',
  religion: '',
  marital_status: '',
  address: '',
  rt: '',
  rw: '',
  family_card_no: '',
  occupation: '',
  education: '',
  citizenship: 'WNI',
  hamlet_id: '',
  family_relation: '',
});

const religionOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const maritalOptions = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];

function nextStep() {
  form.clearErrors('email', 'password', 'password_confirmation');

  let hasError = false;
  if (!form.email) {
    form.setError('email', 'Email wajib diisi.');
    hasError = true;
  }
  if (!form.password) {
    form.setError('password', 'Password wajib diisi.');
    hasError = true;
  } else if (form.password.length < 8) {
    form.setError('password', 'Password minimal 8 karakter.');
    hasError = true;
  }
  if (form.password !== form.password_confirmation) {
    form.setError('password_confirmation', 'Konfirmasi password tidak cocok.');
    hasError = true;
  }

  if (!hasError) {
    step.value = 2;
  }
}

function prevStep() {
  step.value = 1;
}

function submit() {
  form.post('/register', {
    onError: (errors) => {
      // If error belongs to step 1, jump back to step 1
      if (errors.email || errors.password || errors.password_confirmation) {
        step.value = 1;
      }
    },
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>

<template>
  <Head title="Pendaftaran Akun Warga" />

  <GuestLayout
    title="Registrasi Warga Desa"
    subtitle="Lengkapi data akun dan biodata KTP Anda untuk pendaftaran layanan mandiri"
  >
    <!-- Step Progress Indicator -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div
            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-xs"
            :class="step === 1 ? 'bg-teal-600 text-white' : 'bg-teal-100 text-teal-800'"
          >
            1
          </div>
          <span class="text-xs font-bold" :class="step === 1 ? 'text-slate-900' : 'text-slate-500'">
            Akun Login
          </span>
        </div>

        <div class="flex-1 h-0.5 mx-4 bg-slate-200">
          <div
            class="h-full bg-teal-600 transition-all duration-300"
            :class="step === 2 ? 'w-full' : 'w-0'"
          ></div>
        </div>

        <div class="flex items-center gap-2">
          <div
            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-xs"
            :class="step === 2 ? 'bg-teal-600 text-white' : 'bg-slate-200 text-slate-600'"
          >
            2
          </div>
          <span class="text-xs font-bold" :class="step === 2 ? 'text-slate-900' : 'text-slate-500'">
            Data Diri (KTP)
          </span>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- ==================== STEP 1 ==================== -->
      <div v-show="step === 1" class="space-y-4">
        <FormField label="Email" id="email" :error="form.errors.email" required help="Email ini akan digunakan untuk masuk ke akun Anda">
          <input
            id="email"
            type="email"
            v-model="form.email"
            autocomplete="email"
            required
            placeholder="contoh: nama@email.com"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            :class="{ 'border-rose-300 ring-rose-200': form.errors.email }"
          />
        </FormField>

        <FormField label="Password" id="password" :error="form.errors.password" required help="Minimal 8 karakter">
          <div class="relative">
            <input
              id="password"
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              autocomplete="new-password"
              required
              placeholder="Minimal 8 karakter"
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
              <!-- Mata Terbuka -->
              <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <!-- Mata Tercoret -->
              <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            </button>
          </div>
        </FormField>

        <FormField label="Konfirmasi Password" id="password_confirmation" :error="form.errors.password_confirmation" required>
          <div class="relative">
            <input
              id="password_confirmation"
              :type="showPasswordConfirmation ? 'text' : 'password'"
              v-model="form.password_confirmation"
              autocomplete="new-password"
              required
              placeholder="Ulangi password di atas"
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
              <!-- Mata Terbuka -->
              <svg v-if="!showPasswordConfirmation" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <!-- Mata Tercoret -->
              <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            </button>
          </div>
        </FormField>

        <div class="pt-4 flex justify-end">
          <button
            type="button"
            @click="nextStep"
            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm shadow-sm hover:shadow-md transition-all flex items-center justify-center"
          >
            Lanjut ke Data Diri KTP
          </button>
        </div>
      </div>

      <!-- ==================== STEP 2 ==================== -->
      <div v-show="step === 2" class="space-y-4">
        <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs text-amber-900 flex items-start gap-2.5">
          <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <p><strong>Perhatian:</strong> Pastikan NIK dan data diri yang Anda masukkan sesuai dengan data resmi pada Kartu Tanda Penduduk (KTP).</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <FormField label="Nomor Induk Kependudukan (NIK)" id="nik" :error="form.errors.nik" required>
            <input
              id="nik"
              type="text"
              maxlength="16"
              v-model="form.nik"
              required
              placeholder="16 digit NIK KTP"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs font-mono"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.nik }"
            />
          </FormField>

          <FormField label="Nomor Kartu Keluarga (KK)" id="family_card_no" :error="form.errors.family_card_no">
            <input
              id="family_card_no"
              type="text"
              maxlength="16"
              v-model="form.family_card_no"
              placeholder="16 digit No KK (opsional)"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs font-mono"
            />
          </FormField>
        </div>

        <FormField label="Nama Lengkap" id="name" :error="form.errors.name" required>
          <input
            id="name"
            type="text"
            v-model="form.name"
            required
            placeholder="Sesuai KTP"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            :class="{ 'border-rose-300 ring-rose-200': form.errors.name }"
          />
        </FormField>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <FormField label="Tempat Lahir" id="birth_place" :error="form.errors.birth_place" required>
            <input
              id="birth_place"
              type="text"
              v-model="form.birth_place"
              required
              placeholder="Kota / Kabupaten lahir"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.birth_place }"
            />
          </FormField>

          <FormField label="Tanggal Lahir" id="birth_date" :error="form.errors.birth_date" required>
            <input
              id="birth_date"
              type="date"
              v-model="form.birth_date"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.birth_date }"
            />
          </FormField>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <FormField label="Jenis Kelamin" id="gender" :error="form.errors.gender" required>
            <select
              id="gender"
              v-model="form.gender"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs bg-white"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.gender }"
            >
              <option value="" disabled>Pilih Jenis Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </FormField>

          <FormField label="Agama" id="religion" :error="form.errors.religion" required>
            <select
              id="religion"
              v-model="form.religion"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs bg-white"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.religion }"
            >
              <option value="" disabled>Pilih Agama</option>
              <option v-for="r in religionOptions" :key="r" :value="r">{{ r }}</option>
            </select>
          </FormField>

          <FormField label="Status Perkawinan" id="marital_status" :error="form.errors.marital_status" required>
            <select
              id="marital_status"
              v-model="form.marital_status"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs bg-white"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.marital_status }"
            >
              <option value="" disabled>Pilih Status</option>
              <option v-for="m in maritalOptions" :key="m" :value="m">{{ m }}</option>
            </select>
          </FormField>
        </div>

        <FormField label="Alamat Lengkap" id="address" :error="form.errors.address" required>
          <textarea
            id="address"
            rows="2"
            v-model="form.address"
            required
            placeholder="Jalan, Gang, No. Rumah"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            :class="{ 'border-rose-300 ring-rose-200': form.errors.address }"
          ></textarea>
        </FormField>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <FormField label="Dusun" id="hamlet_id" :error="form.errors.hamlet_id">
            <select
              id="hamlet_id"
              v-model="form.hamlet_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs bg-white"
            >
              <option value="">Pilih Dusun (opsional)</option>
              <option v-for="h in hamlets" :key="h.id" :value="h.id">{{ h.name }}</option>
            </select>
          </FormField>

          <FormField label="RT" id="rt" :error="form.errors.rt" required>
            <input
              id="rt"
              type="text"
              maxlength="3"
              v-model="form.rt"
              required
              placeholder="Contoh: 001"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.rt }"
            />
          </FormField>

          <FormField label="RW" id="rw" :error="form.errors.rw" required>
            <input
              id="rw"
              type="text"
              maxlength="3"
              v-model="form.rw"
              required
              placeholder="Contoh: 002"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
              :class="{ 'border-rose-300 ring-rose-200': form.errors.rw }"
            />
          </FormField>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <FormField label="Pekerjaan" id="occupation" :error="form.errors.occupation">
            <input
              id="occupation"
              type="text"
              v-model="form.occupation"
              placeholder="Contoh: Wiraswasta, Petani, Karyawan"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            />
          </FormField>

          <FormField label="Pendidikan Terakhir" id="education" :error="form.errors.education">
            <input
              id="education"
              type="text"
              v-model="form.education"
              placeholder="Contoh: SMA/SMK, S1"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-hidden focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-2xs"
            />
          </FormField>
        </div>

        <div class="pt-4 flex items-center justify-between gap-3">
          <button
            type="button"
            @click="prevStep"
            class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-sm transition"
          >
            Kembali ke Akun
          </button>

          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm shadow-sm hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <span v-if="form.processing">Mendaftarkan Akun...</span>
            <span v-else>Daftar Sekarang</span>
          </button>
        </div>
      </div>

      <div class="pt-4 text-center border-t border-slate-100">
        <p class="text-xs text-slate-600">
          Sudah memiliki akun?
          <Link href="/login" class="font-bold text-teal-600 hover:text-teal-700 transition ml-1">
            Masuk ke Sistem
          </Link>
        </p>
      </div>
    </form>
  </GuestLayout>
</template>

