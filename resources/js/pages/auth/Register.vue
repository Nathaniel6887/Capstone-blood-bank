<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldAlert } from '@lucide/vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: {
        title: 'Registration Disabled',
        description: 'Account registration is managed by Blood Bank administrators.',
    },
});
</script>

<template>
    <Head title="Registration Disabled" />

    <div class="flex flex-col items-center justify-center gap-4 text-center py-4">
        <div class="flex size-12 items-center justify-center rounded-full bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
            <ShieldAlert class="size-6" />
        </div>
        <div>
            <h2 class="text-base font-semibold">Public Registration is Closed</h2>
            <p class="text-xs text-muted-foreground mt-1 max-w-xs">
                To obtain an account, please contact the Caraga Regional Hospital Blood Bank system administrator.
            </p>
        </div>
        <Button as-child class="mt-2 w-full bg-red-700 hover:bg-red-800 text-white">
            <Link href="/">Back to Login</Link>
        </Button>
    </div>
</template>
