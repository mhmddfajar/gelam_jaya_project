<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  pagination: {
    type: Object,
    default: null,
  },
  emptyText: {
    type: String,
    default: 'Tidak ada data yang ditemukan.',
  },
  isEmpty: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200/70 shadow-xs overflow-hidden">
    <div class="overflow-x-auto w-full">
      <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
        <thead class="bg-slate-50/90 text-slate-500 font-bold uppercase text-[11px] tracking-wider border-b border-slate-100 whitespace-nowrap">
          <slot name="header" />
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-800">
          <slot />
          <tr v-if="isEmpty">
            <td colspan="100" class="px-6 py-12 text-center text-slate-400 font-medium">
              {{ emptyText }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div
      v-if="pagination && pagination.links && pagination.links.length > 3"
      class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3 bg-slate-50/50"
    >
      <div class="text-xs text-slate-400">
        Menampilkan <span class="font-bold text-slate-700">{{ pagination.from || 0 }}</span> sampai
        <span class="font-bold text-slate-700">{{ pagination.to || 0 }}</span> dari
        <span class="font-bold text-slate-700">{{ pagination.total || 0 }}</span> data
      </div>

      <div class="flex items-center gap-1">
        <template v-for="(link, index) in pagination.links" :key="index">
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
            :class="link.active ? 'bg-teal-600 text-white shadow-2xs font-bold' : 'bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
            v-html="link.label"
          />
          <span
            v-else
            class="px-3 py-1.5 text-xs text-slate-300 select-none"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </div>
</template>

