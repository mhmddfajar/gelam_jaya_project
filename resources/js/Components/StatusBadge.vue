<script setup>
const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <span
    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide"
    :class="{
      'bg-blue-100 text-blue-800 border border-blue-200': status === 'diajukan',
      'bg-amber-100 text-amber-800 border border-amber-200': status === 'perlu_perbaikan' || status === 'diproses',
      'bg-indigo-100 text-indigo-800 border border-indigo-200': status === 'diverifikasi',
      'bg-teal-100 text-teal-800 border border-teal-200': status === 'siap_diambil',
      'bg-emerald-100 text-emerald-800 border border-emerald-200': status === 'selesai' || status === 'active',
      'bg-rose-100 text-rose-800 border border-rose-200': status === 'ditolak' || status === 'inactive',
      'bg-slate-100 text-slate-800 border border-slate-200': !['diajukan','perlu_perbaikan','diproses','diverifikasi','siap_diambil','selesai','ditolak','active','inactive'].includes(status),
    }"
  >
    <span
      class="w-1.5 h-1.5 rounded-full mr-1.5"
      :class="{
        'bg-blue-500': status === 'diajukan',
        'bg-amber-500': status === 'perlu_perbaikan' || status === 'diproses',
        'bg-indigo-500': status === 'diverifikasi',
        'bg-teal-500': status === 'siap_diambil',
        'bg-emerald-500': status === 'selesai' || status === 'active',
        'bg-rose-500': status === 'ditolak' || status === 'inactive',
        'bg-slate-400': !['diajukan','perlu_perbaikan','diproses','diverifikasi','siap_diambil','selesai','ditolak','active','inactive'].includes(status),
      }"
    ></span>
    {{ label || status }}
  </span>
</template>

