<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import hospitalLogo from '@/../../resources/images/caraga-regional-hospital20220211.webp';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        :src="hospitalLogo"
        alt="Caraga Regional Hospital Logo"
        :class="['object-contain shrink-0', className]"
        v-bind="$attrs"
    />
</template>
