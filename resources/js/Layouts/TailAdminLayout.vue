<template>
    <div class="min-h-screen xl:flex bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
      <AppSidebar />
      <Backdrop />
      <div
        class="flex-1 transition-all duration-300 ease-in-out flex flex-col min-h-screen"
        :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']"
      >
        <AppHeader />
        
        <ToastNotification />

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
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppSidebar from '@/Components/TailAdmin/AppSidebar.vue'
import AppHeader from '@/Components/TailAdmin/AppHeader.vue'
import Backdrop from '@/Components/TailAdmin/Backdrop.vue'
import { useSidebarProvider } from '@/Composables/useSidebar'
import ToastNotification from '@/Components/ToastNotification.vue'
import { useNotificationStore } from '@/stores/notification'

const props = defineProps({
  title: String,
})

// Initialize Sidebar Provider for the whole tree
const { isExpanded, isHovered } = useSidebarProvider()

// Toast & Flash handler
const page = usePage()
const notificationStore = useNotificationStore()

watch(() => page.props.flash, (flash) => {
  const errors = page.props.errors
  if (flash?.success) notificationStore.success(flash.success)
  else if (flash?.error) notificationStore.error(flash.error)
  else if (flash?.warning) notificationStore.warning(flash.warning)
  else if (flash?.info) notificationStore.info(flash.info)
  else if (errors && Object.keys(errors).length > 0) {
    const msgs = Object.values(errors).flat()
    notificationStore.error(msgs[0] || 'Terdapat kesalahan pada formulir.')
  }
}, { deep: true, immediate: true })
</script>
