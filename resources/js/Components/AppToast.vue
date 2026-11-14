<script setup>
import { ref, watch, computed, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const flash = computed(() => page.props.flash || {});

const show = ref(false);
const message = ref('');
const type = ref('success'); // success, error, info, warning

let timeoutId = null;

function clearToast() {
  show.value = false;
  if (timeoutId) {
    clearTimeout(timeoutId);
    timeoutId = null;
  }
}

function triggerToast(msg, toastType) {
  if (!msg) return;
  message.value = msg;
  type.value = toastType;
  show.value = true;

  if (timeoutId) clearTimeout(timeoutId);
  timeoutId = setTimeout(() => {
    show.value = false;
    timeoutId = null;
  }, 3500); // auto-dismiss 3.5 detik (3-4s sesuai instruksi)
}

watch(
  flash,
  (newFlash) => {
    if (newFlash.success) {
      triggerToast(newFlash.success, 'success');
    } else if (newFlash.status) {
      triggerToast(newFlash.status, 'success');
    } else if (newFlash.error) {
      triggerToast(newFlash.error, 'error');
    } else if (newFlash.warning) {
      triggerToast(newFlash.warning, 'warning');
    } else if (newFlash.info) {
      triggerToast(newFlash.info, 'info');
    }
  },
  { deep: true, immediate: true }
);

// Reset toast saat URL berubah agar pesan tidak terus menempel saat navigasi
watch(
  () => page.url,
  (newUrl, oldUrl) => {
    if (newUrl !== oldUrl) {
      clearToast();
    }
  }
);

// Bersihkan toast saat navigasi dimulai
const removeStartListener = router.on('start', () => {
  clearToast();
});

onUnmounted(() => {
  if (timeoutId) clearTimeout(timeoutId);
  if (removeStartListener) removeStartListener();
});

function close() {
  clearToast();
}
</script>

<template>
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed bottom-5 right-5 z-50 max-w-sm w-full rounded-2xl shadow-lg border p-4 flex items-start gap-3 backdrop-blur-md"
      :class="{
        'bg-emerald-50/95 border-emerald-300 text-emerald-900': type === 'success',
        'bg-rose-50/95 border-rose-300 text-rose-900': type === 'error',
        'bg-amber-50/95 border-amber-300 text-amber-900': type === 'warning',
        'bg-sky-50/95 border-sky-300 text-sky-900': type === 'info',
      }"
    >
      <div class="shrink-0 mt-0.5">
        <span v-if="type === 'success'" class="inline-block w-5 h-5 text-emerald-600 font-bold">✓</span>
        <span v-else-if="type === 'error'" class="inline-block w-5 h-5 text-rose-600 font-bold">✕</span>
        <span v-else-if="type === 'warning'" class="inline-block w-5 h-5 text-amber-600 font-bold">!</span>
        <span v-else class="inline-block w-5 h-5 text-sky-600 font-bold">ℹ</span>
      </div>

      <div class="flex-1 text-sm font-medium">
        {{ message }}
      </div>

      <button
        type="button"
        @click="close"
        class="shrink-0 text-slate-400 hover:text-slate-600 transition text-sm font-bold p-0.5"
      >
        ✕
      </button>
    </div>
  </transition>
</template>
