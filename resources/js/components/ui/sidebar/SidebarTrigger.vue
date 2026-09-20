<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { Menu } from "@lucide/vue"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="outline"
    size="icon"
    :class="cn('size-9 rounded-lg border-slate-200/90 bg-white text-slate-700 shadow-2xs hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700', props.class)"
    @click="toggleSidebar"
  >
    <Menu class="size-4.5" />
    <span class="sr-only">Toggle sidebar</span>
  </Button>
</template>
