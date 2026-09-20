<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center justify-between border-b bg-white px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-14 md:px-6 dark:bg-neutral-900 dark:border-neutral-800"
    >
        <!-- Left Section: Toggle & Breadcrumbs -->
        <div class="flex items-center gap-3">
            <SidebarTrigger />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" class="hidden sm:flex" />
            </template>
        </div>

        <!-- Right Section: User Profile Pill Dropdown -->
        <div class="flex items-center gap-3">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="flex items-center gap-2.5 rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-left shadow-2xs transition-all hover:bg-slate-50 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-750"
                    >
                        <!-- Avatar Initials Box -->
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-slate-200 text-xs font-bold text-slate-700 dark:bg-neutral-700 dark:text-neutral-200"
                        >
                            {{ getInitials(auth?.user?.name || 'Admin') }}
                        </div>

                        <!-- Username -->
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ auth?.user?.name || 'Admin' }}
                        </span>

                        <!-- Down Chevron Icon -->
                        <ChevronDown class="size-4 text-slate-400" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56 mt-1 shadow-lg rounded-xl border border-slate-200 dark:border-neutral-800">
                    <UserMenuContent v-if="auth?.user" :user="auth.user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
