<template>
  <div
    v-if="isMobileOpen"
    class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm transition-opacity lg:hidden dark:bg-gray-900/80"
    @click="toggleMobileSidebar"
  />
</template>

<script setup>
import { useSidebar } from '@/Composables/useSidebar'

const { isMobileOpen, toggleMobileSidebar } = useSidebar()
</script>
