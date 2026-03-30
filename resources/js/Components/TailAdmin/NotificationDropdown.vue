<template>
  <div class="relative" ref="dropdownRef">
    <!-- Trigger Button -->
    <button 
      @click="isOpen = !isOpen"
      class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-indigo-600 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
    >
      <span v-if="unreadCount > 0" class="absolute right-0 top-0.5 z-10 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white dark:border-gray-900">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" fill="currentColor" />
      </svg>
    </button>

    <!-- Dropdown Panel -->
    <div 
      v-if="isOpen" 
      class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-50 overflow-hidden text-left"
    >
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Notifikasi</h3>
        <button 
          v-if="unreadCount > 0" 
          @click="markAllAsRead" 
          class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
        >
          Tandai semua dibaca
        </button>
      </div>

      <div class="max-h-[320px] overflow-y-auto">
        <template v-if="notifications.length > 0">
          <div 
            v-for="notif in notifications" 
            :key="notif.id"
            class="group px-4 py-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer relative"
          >
            <!-- Unread Indicator -->
            <div v-if="!notif.is_read" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-500 rounded-r-md"></div>
            
            <div class="pl-2" @click="handleNotificationClick(notif)">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">{{ notif.type.replace('_', ' ') }}</span>
                <span class="text-[10px] text-gray-400">{{ formatDate(notif.created_at) }}</span>
              </div>
              <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-0.5" :class="notif.is_read ? 'text-opacity-80' : ''">{{ notif.title }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" :class="notif.is_read ? 'text-opacity-80' : ''">{{ notif.message }}</p>
            </div>
            
            <!-- Context action: read dot -->
            <button 
              v-if="!notif.is_read"
              @click.stop="markAsRead(notif.id)"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
              title="Tandai dibaca"
            >
              <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </button>
          </div>
        </template>
        
        <div v-else class="px-4 py-8 text-center flex flex-col items-center justify-center">
          <svg width="32" height="32" fill="none" viewBox="0 0 24 24" class="text-gray-300 dark:text-gray-600 mb-2">
            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
          </svg>
          <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru 🎉</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const isOpen = ref(false)
const dropdownRef = ref(null)

// Computed reactive props from Inertia global state
const unreadCount = computed(() => page.props.auth?.notifications?.unreadCount || 0)
const notifications = computed(() => page.props.auth?.notifications?.latest || [])

// Click outside handler
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffInMinutes = Math.floor((now - date) / (1000 * 60))
  
  if (diffInMinutes < 60) {
    return `${diffInMinutes} m yang lalu`
  }
  
  const diffInHours = Math.floor(diffInMinutes / 60)
  if (diffInHours < 24) {
    return `${diffInHours} j yang lalu`
  }
  
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

const markAsRead = (id) => {
  router.post(route('notifications.read', id), {}, {
    preserveScroll: true,
    preserveState: true,
  })
}

const markAllAsRead = () => {
  router.post(route('notifications.read-all'), {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isOpen.value = false
    }
  })
}

const handleNotificationClick = (notif) => {
  if (!notif.is_read) {
    markAsRead(notif.id)
  }
  
  // Close the menu
  isOpen.value = false
  
  // Navigate if URL exists
  if (notif.url) {
    router.visit(notif.url)
  }
}
</script>
