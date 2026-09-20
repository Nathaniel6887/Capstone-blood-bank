<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    BarChart3, 
    ChevronDown, 
    Droplets, 
    LayoutGrid, 
    Menu, 
    SquarePlus, 
    Users 
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { isCurrentUrl } = useCurrentUrl();

const mainNavItems: NavItem[] = [
    {
        title: 'Overview',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Blood Collection',
        href: '/blood-collection',
        icon: Droplets,
    },
    {
        title: 'Blood Consumption',
        href: '/blood-consumtion',
        icon: SquarePlus,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: BarChart3,
    },
    {
        title: 'Staff Accounts',
        href: '/staff-accounts',
        icon: Users,
    },
];
</script>

<template>
    <div>
        <div class="border-b border-slate-200/80 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="mx-auto flex h-16 items-center justify-between px-4 lg:px-8">
                <!-- Left Section: Mobile Menu & Logo -->
                <div class="flex items-center gap-3">
                    <div class="lg:hidden">
                        <Sheet>
                            <SheetTrigger :as-child="true">
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="size-9 rounded-lg border-slate-200/90 bg-white text-slate-700 shadow-2xs hover:bg-slate-100"
                                >
                                    <Menu class="size-4.5" />
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="left" class="w-[300px] p-6 bg-white dark:bg-neutral-900">
                                <SheetTitle class="sr-only">Navigation menu</SheetTitle>
                                <SheetHeader class="flex justify-start text-left pb-4 border-b border-slate-100 dark:border-neutral-800">
                                    <Link :href="dashboard()" class="flex items-center gap-2">
                                        <AppLogo />
                                    </Link>
                                </SheetHeader>
                                <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-4">
                                    <nav class="space-y-1.5">
                                        <Link
                                            v-for="item in mainNavItems"
                                            :key="item.title"
                                            :href="item.href"
                                            class="flex items-center gap-3.5 rounded-xl px-4 py-3 text-[15px] font-semibold transition-all duration-150"
                                            :class="[
                                                isCurrentUrl(item.href)
                                                    ? 'bg-[#eaf2fe] text-[#1e40af] font-bold dark:bg-blue-950/60 dark:text-blue-300'
                                                    : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-neutral-800',
                                            ]"
                                        >
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="size-5 shrink-0"
                                                :class="[
                                                    isCurrentUrl(item.href)
                                                        ? 'text-[#1e40af] dark:text-blue-300'
                                                        : 'text-slate-400 dark:text-slate-400',
                                                ]"
                                            />
                                            {{ item.title }}
                                        </Link>
                                    </nav>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>

                    <Link :href="dashboard()" class="flex items-center gap-x-2">
                        <AppLogo />
                    </Link>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden h-full lg:flex lg:items-center">
                    <nav class="flex items-center gap-1.5">
                        <Link
                            v-for="item in mainNavItems"
                            :key="item.title"
                            :href="item.href"
                            class="flex items-center gap-2.5 rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-150"
                            :class="[
                                isCurrentUrl(item.href)
                                    ? 'bg-[#eaf2fe] text-[#1e40af] font-bold dark:bg-blue-950/60 dark:text-blue-300'
                                    : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-neutral-800',
                            ]"
                        >
                            <component
                                v-if="item.icon"
                                :is="item.icon"
                                class="size-4.5 shrink-0"
                                :class="[
                                    isCurrentUrl(item.href)
                                        ? 'text-[#1e40af] dark:text-blue-300'
                                        : 'text-slate-400 dark:text-slate-400',
                                ]"
                            />
                            {{ item.title }}
                        </Link>
                    </nav>
                </div>

                <!-- Right Section: User Profile Pill Dropdown -->
                <div class="flex items-center gap-3">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <button
                                type="button"
                                class="flex items-center gap-2.5 rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-left shadow-2xs transition-all hover:bg-slate-50 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-750"
                            >
                                <div
                                    class="flex size-7 items-center justify-center rounded-md bg-slate-200 text-xs font-bold text-slate-700 dark:bg-neutral-700 dark:text-neutral-200"
                                >
                                    {{ getInitials(auth?.user?.name || 'Admin') }}
                                </div>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                    {{ auth?.user?.name || 'Admin' }}
                                </span>
                                <ChevronDown class="size-4 text-slate-400" />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 mt-1 shadow-lg rounded-xl border border-slate-200 dark:border-neutral-800">
                            <UserMenuContent v-if="auth?.user" :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="border-sidebar-border/70 flex w-full border-b bg-slate-50/50 dark:bg-neutral-900/50"
        >
            <div
                class="mx-auto flex h-10 w-full items-center justify-start px-4 text-slate-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
