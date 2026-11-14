<script setup>
defineProps({
  label: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  help: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <div class="space-y-1.5">
    <label v-if="label" :for="id" class="block text-sm font-semibold text-slate-700">
      {{ label }}
      <span v-if="required" class="text-rose-500 font-bold">*</span>
    </label>

    <slot />

    <p v-if="help && !error" class="text-xs text-slate-500">
      {{ help }}
    </p>

    <p v-if="error" class="text-xs font-medium text-rose-600 flex items-center gap-1 mt-1">
      <span class="inline-block w-3.5 h-3.5 text-center leading-tight rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">!</span>
      {{ error }}
    </p>
  </div>
</template>

