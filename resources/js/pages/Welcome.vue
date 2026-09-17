<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    router.visit('/');
});
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50">
        <div class="animate-pulse text-gray-500">Loading Caraga Regional Hospital Blood Bank...</div>
    </div>
</template>
