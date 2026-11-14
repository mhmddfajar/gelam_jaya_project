<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Konfirmasi Aksi',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan aksi ini?',
  },
  confirmText: {
    type: String,
    default: 'Ya, Lanjutkan',
  },
  cancelText: {
    type: String,
    default: 'Batal',
  },
  danger: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['confirm', 'close']);
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="$emit('close')"></div>

    <!-- Modal Dialog -->
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 transform transition-all z-10 space-y-4">
      <div class="flex items-start gap-4">
        <div
          class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
          :class="danger ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'"
        >
          <span class="text-xl font-bold">!</span>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900">{{ title }}</h3>
          <p class="text-sm text-slate-600 mt-1">{{ message }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
        <button
          type="button"
          :disabled="loading"
          @click="$emit('close')"
          class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition disabled:opacity-50"
        >
          {{ cancelText }}
        </button>
        <button
          type="button"
          :disabled="loading"
          @click="$emit('confirm')"
          class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition disabled:opacity-50"
          :class="danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'"
        >
          <span v-if="loading">Memproses...</span>
          <span v-else>{{ confirmText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

