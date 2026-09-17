<script setup lang="ts">
import hospitalLogo from '@/../../resources/images/caraga-regional-hospital20220211.webp';
</script>

<template>
    <div
        class="flex aspect-square size-9 shrink-0 items-center justify-center rounded-xl bg-white shadow-xs p-1 border border-border/50"
    >
        <img :src="hospitalLogo" alt="Caraga Regional Hospital Logo" class="size-full object-contain" />
    </div>
    <div class="ml-1.5 grid flex-1 text-left">
        <span class="truncate leading-tight font-black text-xs tracking-tight text-gray-900 dark:text-white uppercase">
            CRH BLOOD BANK
        </span>
        <span class="truncate text-[10px] text-muted-foreground font-semibold uppercase tracking-wider">
            Caraga Regional Hospital
        </span>
    </div>
</template>
