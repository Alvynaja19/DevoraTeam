<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="flex items-center text-gray-700 dark:text-gray-400 gap-3"
      @click.prevent="toggleDropdown"
    >
      <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm bg-gradient-to-br from-[#2b5a41] to-[#1c3b2b]">
        {{ userInitials }}
      </div>

      <div class="hidden sm:block text-left mr-1">
        <span class="block font-medium text-theme-sm">{{ $page.props.auth.user?.name }}</span>
        <span class="block text-xs text-gray-500 capitalize">{{ $page.props.auth.user?.role }}</span>
      </div>

      <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" class="w-4 h-4 transition-transform duration-200" />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-[#111827] z-50"
    >
      <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-800 mb-2">
        <span class="block font-medium text-gray-700 text-sm dark:text-gray-200">
          {{ $page.props.auth.user?.name }}
        </span>
        <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">
          {{ $page.props.auth.user?.email }}
        </span>
      </div>

      <ul class="flex flex-col gap-1 pb-3 border-b border-gray-200 dark:border-gray-800">
        <li v-for="item in menuItems" :key="item.href">
          <Link
            :href="item.href"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white"
          >
            <component
              :is="item.icon"
              class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
            />
            {{ item.text }}
          </Link>
        </li>
      </ul>
      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="flex items-center gap-3 px-3 py-2 mt-3 font-medium text-red-600 rounded-lg group text-theme-sm hover:bg-red-50 dark:hover:bg-red-500/10"
      >
        <LogoutIcon class="w-5 h-5 text-red-500 group-hover:text-red-700" />
        Keluar
      </Link>
    </div>
    <!-- Dropdown End -->
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import UserCircleIcon from '@/Components/TailAdminIcons/UserCircleIcon.vue'
import ChevronDownIcon from '@/Components/TailAdminIcons/ChevronDownIcon.vue'
import LogoutIcon from '@/Components/TailAdminIcons/LogoutIcon.vue'
import SettingsIcon from '@/Components/TailAdminIcons/SettingsIcon.vue'

const page = usePage()
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const userInitials = computed(() => {
  const name = page.props.auth.user?.name || 'User'
  return name.substring(0, 2).toUpperCase()
})

const menuItems = [
  { href: '#', icon: UserCircleIcon, text: 'Profil Saya' },
  { href: route('settings.index'), icon: SettingsIcon, text: 'Pengaturan' },
]

const toggleDropdown = () => { dropdownOpen.value = !dropdownOpen.value }
const closeDropdown = () => { dropdownOpen.value = false }

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
