<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Droplets, 
    SquarePlus, 
    BarChart3, 
    Users 
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

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
    <Sidebar collapsible="icon" variant="sidebar" class="border-r border-slate-200/80 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <SidebarHeader class="h-16 flex items-center justify-center border-b border-slate-100 dark:border-neutral-800 px-4">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="hover:bg-transparent">
                        <Link :href="dashboard()" class="flex items-center gap-2.5">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="p-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>
    </Sidebar>
    <slot />
</template>
