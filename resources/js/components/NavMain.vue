<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
const { state, isMobile } = useSidebar();
</script>

<template>
    <TooltipProvider :delay-duration="0">
        <nav class="space-y-1.5">
            <template v-for="item in items" :key="item.title">
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Link
                            :href="item.href"
                            class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-[15px] font-semibold transition-all duration-150"
                            :class="[
                                isCurrentUrl(item.href)
                                    ? 'bg-[#eaf2fe] text-[#1e40af] font-bold shadow-2xs dark:bg-blue-950/60 dark:text-blue-300'
                                    : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-neutral-800/80 dark:hover:text-neutral-100',
                                state === 'collapsed' && !isMobile ? 'justify-center px-2 py-2.5' : '',
                            ]"
                        >
                            <component
                                :is="item.icon"
                                class="size-5 shrink-0 transition-colors"
                                :class="[
                                    isCurrentUrl(item.href)
                                        ? 'text-[#1e40af] dark:text-blue-300'
                                        : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-400 dark:group-hover:text-slate-200',
                                ]"
                            />
                            <span v-if="state !== 'collapsed' || isMobile" class="truncate tracking-tight">
                                {{ item.title }}
                            </span>
                        </Link>
                    </TooltipTrigger>
                    <TooltipContent
                        v-if="state === 'collapsed' && !isMobile"
                        side="right"
                        align="center"
                        class="font-medium"
                    >
                        {{ item.title }}
                    </TooltipContent>
                </Tooltip>
            </template>
        </nav>
    </TooltipProvider>
</template>
