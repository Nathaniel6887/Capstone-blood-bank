<script setup lang="ts">
import hospitalLogo from '@/../../resources/images/caraga-regional-hospital20220211.webp';
</script>

<template>
    <div class="flex items-center gap-2.5">
        <div
            class="flex aspect-square size-9 shrink-0 items-center justify-center rounded-full bg-white shadow-2xs p-0.5 border border-slate-200/80 dark:border-neutral-700 dark:bg-neutral-800"
        >
            <img :src="hospitalLogo" alt="Caraga Regional Hospital Logo" class="size-full rounded-full object-contain" />
        </div>
        <div class="grid flex-1 text-left">
            <span class="truncate text-sm font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">
                CRH Blood Bank
            </span>
            <span class="truncate text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                Admin Dashboard
            </span>
        </div>
    </div>
</template>
