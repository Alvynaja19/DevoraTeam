<template>
  <SidebarProvider>
    <div class="min-h-screen xl:flex bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
      <AppSidebar />
      <Backdrop />
      <div
        class="flex-1 transition-all duration-300 ease-in-out flex flex-col min-h-screen"
        :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']"
      >
        <AppHeader />
        
        <!-- Flash Message Bawaan DevoraV2 -->
        <div v-if="$page.props.flash.success || $page.props.flash.error" class="px-4 md:px-6 pt-4 mx-auto max-w-(--breakpoint-2xl) w-full">
          <div v-if="$page.props.flash.success" class="flex items-center gap-3 p-4 rounded-xl mb-2" style="background:#f0fdf4; border:1px solid #bbf7d0">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</div>
          </div>
          <div v-if="$page.props.flash.error" class="flex items-center gap-3 p-4 rounded-xl mb-2" style="background:#fef2f2; border:1px solid #fecaca">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke="#dc2626" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</div>
          </div>
        </div>

        <main class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 flex-1 w-full">
          <!-- Area Topbar Actions (opsional di slot Layout Devora) -->
          <div v-if="$slots['topbar-actions'] || title" class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <h1 v-if="title" class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ title }}</h1>
            <slot name="topbar-actions"></slot>
          </div>

          <slot></slot>
        </main>
      </div>
    </div>
  </SidebarProvider>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppSidebar from '@/Components/TailAdmin/AppSidebar.vue'
import AppHeader from '@/Components/TailAdmin/AppHeader.vue'
import Backdrop from '@/Components/TailAdmin/Backdrop.vue'
import { useSidebarProvider } from '@/Composables/useSidebar'

const props = defineProps({
  title: String,
})

// Initialize Sidebar Provider for the whole tree
const { isExpanded, isHovered } = useSidebarProvider()
</script>
