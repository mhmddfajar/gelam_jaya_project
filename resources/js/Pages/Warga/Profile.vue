<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import WargaLayout from '@/Layouts/WargaLayout.vue';
import FormField from '@/Components/FormField.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  resident: {
    type: Object,
    default: null,
  },
  hamlets: {
    type: Array,
    default: () => [],
  },
});

const isVerified = computed(() => Boolean(props.resident?.is_verified));

const form = useForm({
  family_card_no: props.resident?.family_card_no || '',
  name: props.resident?.name || '',
  birth_place: props.resident?.birth_place || '',
  birth_date: props.resident?.birth_date || '',
  gender: props.resident?.gender || '',
  religion: props.resident?.religion || '',
  marital_status: props.resident?.marital_status || '',
  occupation: props.resident?.occupation || '',
  education: props.resident?.education || '',
  citizenship: props.resident?.citizenship || 'WNI',
  address: props.resident?.address || '',
  hamlet_id: props.resident?.hamlet_id || '',
  rt: props.resident?.rt || '',
  rw: props.resident?.rw || '',
  family_relation: props.resident?.family_relation || '',
});

const religionOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const maritalOptions = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati', 'Duda', 'Janda'];

function submit() {
  if (isVerified.value) return;
  form.put('/warga/profil');
}
</script>

<template>
  <Head title="Data Diri & Profil" />

  <WargaLayout>
    <template #header>Data Diri & Profil Kependudukan</template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Aesthetic Verification Status Banner -->
      <!-- Version A: Terverifikasi (Emerald / Teal Soft Glow) -->
      <div
        v-if="isVerified"
        class="p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-emerald-50/90 via-teal-50/40 to-white border border-emerald-200/80 shadow-xs flex items-start gap-4 transition-all"
      >
        <div class="w-11 h-11 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-xs shadow-emerald-500/20">
          ✓
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-2.5">
            <h3 class="font-extrabold text-emerald-950 text-base tracking-tight">Data Kependudukan Terverifikasi</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300/70">
              Resmi Valid
            </span>
          </div>
          <p class="text-xs sm:text-sm text-emerald-800/90 mt-1.5 leading-relaxed">
            Data kependudukan Anda telah diverifikasi oleh staf pelayanan Kantor Desa Gelam Jaya. Demi keamanan dan keabsahan arsip desa, perubahan data diri hanya dapat dilakukan melalui petugas di kantor desa.
          </p>
        </div>
      </div>

      <!-- Version B: Belum Terverifikasi (Amber / Warm Soft Glow) -->
      <div
        v-else
        class="p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-amber-50/90 via-orange-50/40 to-white border border-amber-200/80 shadow-xs flex items-start gap-4 transition-all"
      >
        <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-xs shadow-amber-500/20">
          ⚠️
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-2.5">
            <h3 class="font-extrabold text-amber-950 text-base tracking-tight">Data Diri Menunggu Verifikasi Staf</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300/70">
              Belum Diverifikasi
            </span>
          </div>
          <p class="text-xs sm:text-sm text-amber-800/90 mt-1.5 leading-relaxed">
            Data diri Anda saat ini belum diverifikasi oleh petugas desa. Anda masih diperbolehkan memperbarui isian biodata di bawah ini (kecuali NIK). Setelah diverifikasi staf, form akan terkunci otomatis.
          </p>
        </div>
      </div>

      <!-- Main Form Card (Modern Clean Soft Light) -->
      <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs hover:shadow-md transition-all p-6 md:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
          <div>
            <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Biodata KTP Warga</h3>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Pastikan seluruh data sesuai dengan dokumen resmi kependudukan Anda.</p>
          </div>
          <div class="self-start sm:self-auto flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-700 text-xs font-mono font-bold shadow-2xs">
            <span class="text-teal-600 font-sans font-semibold">NIK:</span>
            <span>{{ resident?.nik }}</span>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <!-- NIK & KK Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Nomor Induk Kependudukan (NIK)" id="nik" help="NIK bersifat permanen dan tidak dapat diubah">
              <input
                id="nik"
                type="text"
                :value="resident?.nik"
                disabled
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-500 text-sm font-mono cursor-not-allowed shadow-inner"
              />
            </FormField>

            <FormField label="Nomor Kartu Keluarga (KK)" id="family_card_no" :error="form.errors.family_card_no">
              <input
                id="family_card_no"
                type="text"
                maxlength="16"
                v-model="form.family_card_no"
                :disabled="isVerified"
                placeholder="16 digit Nomor KK"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs font-mono disabled:bg-slate-50 disabled:cursor-not-allowed transition"
                :class="{ 'border-rose-300': form.errors.family_card_no }"
              />
            </FormField>
          </div>

          <!-- Name -->
          <FormField label="Nama Lengkap Sesuai KTP" id="name" :error="form.errors.name" required>
            <input
              id="name"
              type="text"
              v-model="form.name"
              :disabled="isVerified"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              :class="{ 'border-rose-300': form.errors.name }"
            />
          </FormField>

          <!-- TTL -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Tempat Lahir" id="birth_place" :error="form.errors.birth_place" required>
              <input
                id="birth_place"
                type="text"
                v-model="form.birth_place"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
                :class="{ 'border-rose-300': form.errors.birth_place }"
              />
            </FormField>

            <FormField label="Tanggal Lahir" id="birth_date" :error="form.errors.birth_date" required>
              <input
                id="birth_date"
                type="date"
                v-model="form.birth_date"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
                :class="{ 'border-rose-300': form.errors.birth_date }"
              />
            </FormField>
          </div>

          <!-- Gender, Agama, Status -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField label="Jenis Kelamin" id="gender" :error="form.errors.gender" required>
              <select
                id="gender"
                v-model="form.gender"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              >
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </FormField>

            <FormField label="Agama" id="religion" :error="form.errors.religion" required>
              <select
                id="religion"
                v-model="form.religion"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              >
                <option v-for="rel in religionOptions" :key="rel" :value="rel">{{ rel }}</option>
              </select>
            </FormField>

            <FormField label="Status Perkawinan" id="marital_status" :error="form.errors.marital_status" required>
              <select
                id="marital_status"
                v-model="form.marital_status"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              >
                <option v-for="m in maritalOptions" :key="m" :value="m">{{ m }}</option>
              </select>
            </FormField>
          </div>

          <!-- Alamat -->
          <FormField label="Alamat Tempat Tinggal" id="address" :error="form.errors.address" required>
            <textarea
              id="address"
              rows="2"
              v-model="form.address"
              :disabled="isVerified"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              :class="{ 'border-rose-300': form.errors.address }"
            ></textarea>
          </FormField>

          <!-- Dusun, RT, RW -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField label="Dusun" id="hamlet_id" :error="form.errors.hamlet_id">
              <select
                id="hamlet_id"
                v-model="form.hamlet_id"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs bg-white disabled:bg-slate-50 disabled:cursor-not-allowed transition"
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
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>

            <FormField label="RW" id="rw" :error="form.errors.rw" required>
              <input
                id="rw"
                type="text"
                maxlength="3"
                v-model="form.rw"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>
          </div>

          <!-- Pekerjaan, Pendidikan, Hub Keluarga, WN -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField label="Pekerjaan" id="occupation" :error="form.errors.occupation">
              <input
                id="occupation"
                type="text"
                v-model="form.occupation"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>

            <FormField label="Pendidikan Terakhir" id="education" :error="form.errors.education">
              <input
                id="education"
                type="text"
                v-model="form.education"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>

            <FormField label="Hubungan Dalam Keluarga" id="family_relation" :error="form.errors.family_relation">
              <input
                id="family_relation"
                type="text"
                v-model="form.family_relation"
                :disabled="isVerified"
                placeholder="Contoh: Kepala Keluarga, Istri, Anak"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>

            <FormField label="Kewarganegaraan" id="citizenship" :error="form.errors.citizenship">
              <input
                id="citizenship"
                type="text"
                v-model="form.citizenship"
                :disabled="isVerified"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-2xs disabled:bg-slate-50 disabled:cursor-not-allowed transition"
              />
            </FormField>
          </div>

          <!-- Action buttons -->
          <div class="pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100">
            <p v-if="isVerified" class="text-xs text-slate-400 italic">
              🔒 Formulir dikunci karena data kependudukan telah diverifikasi petugas.
            </p>
            <p v-else class="text-xs text-slate-400">
              💡 Periksa kembali kebenaran data Anda sebelum menekan tombol simpan.
            </p>

            <button
              v-if="!isVerified"
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs hover:shadow-md transition-all disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <span v-if="form.processing">Menyimpan...</span>
              <span v-else>Simpan Perubahan Data Diri</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </WargaLayout>
</template>
