<template>
  <div class="min-h-screen flex" style="background:#f8fafc">
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Logo -->
      <div class="sidebar-logo">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#2b5a41,#1c3b2b)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div>
            <div class="text-white font-bold text-base leading-none">SMA 4 JEMBER</div>
            <div class="text-xs mt-0.5" style="color:#475569">Perpustakaan SMA</div>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar-nav">
        <div class="sidebar-group-label">Utama</div>
        <NavItem :href="route('dashboard')" :active="isActive('dashboard')">
          <template #icon><IconDashboard /></template>
          Dashboard
        </NavItem>

        <div class="sidebar-group-label mt-4">Perpustakaan</div>
        <NavItem :href="route('members.index')" :active="isActive('members')">
          <template #icon><IconUsers /></template>
          Anggota
          <span v-if="$page.props.pendingCount > 0" class="ml-auto badge badge-red text-xs">{{ $page.props.pendingCount }}</span>
        </NavItem>
        <NavItem :href="route('books.index')" :active="isActive('books')">
          <template #icon><IconBook /></template>
          Buku
        </NavItem>
        <NavItem :href="route('loans.index')" :active="isActive('loans') || route().current() === 'loans.index'">
          <template #icon><IconLoan /></template>
          Peminjaman
        </NavItem>
        <NavItem :href="route('returns.index')" :active="isActive('returns') || route().current() === 'returns.index'">
          <template #icon><IconReturn /></template>
          Pengembalian
        </NavItem>
        <NavItem :href="route('history.index')" :active="isActive('history') || route().current() === 'history.index'">
          <template #icon><IconDashboard /></template>
          Riwayat Transaksi
        </NavItem>
        <NavItem :href="route('fines.index')" :active="isActive('fines')">
          <template #icon><IconFine /></template>
          Denda
        </NavItem>

        <template v-if="isAdmin">
          <div class="sidebar-group-label mt-4">Admin</div>
          <NavItem :href="route('settings.index')" :active="isActive('settings')">
            <template #icon><IconSettings /></template>
            Pengaturan
          </NavItem>
        </template>
      </nav>

      <!-- User Info -->
      <div class="p-4 border-t" style="border-color:#1e293b">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#2b5a41,#1c3b2b)">
            {{ $page.props.auth.user.name[0].toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-white truncate">{{ $page.props.auth.user.name }}</div>
            <div class="text-xs capitalize" style="color:#475569">{{ $page.props.auth.user.role }}</div>
          </div>
          <Link :href="route('logout')" method="post" as="button" class="p-1.5 rounded-lg transition-colors hover:bg-slate-700 text-slate-400 hover:text-white">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </Link>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <div class="flex-1">
          <h1 v-if="title" class="font-semibold text-slate-800 text-base">{{ title }}</h1>
        </div>
        <slot name="topbar-actions" />
      </header>

      <!-- Content -->
      <main class="page-content fade-in">
        <slot />
      </main>
    </div>

    <!-- Toast Notifications -->
    <ToastNotification />
  </div>
</template>

<style scoped>
</style>

<script setup>
import { computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavItem from '@/Components/NavItem.vue'
import IconDashboard from '@/Components/Icons/IconDashboard.vue'
import IconUsers from '@/Components/Icons/IconUsers.vue'
import IconBook from '@/Components/Icons/IconBook.vue'
import IconLoan from '@/Components/Icons/IconLoan.vue'
import IconReturn from '@/Components/Icons/IconReturn.vue'
import IconFine from '@/Components/Icons/IconFine.vue'
import IconSettings from '@/Components/Icons/IconSettings.vue'
import ToastNotification from '@/Components/ToastNotification.vue'
import { useNotificationStore } from '@/stores/notification'

defineProps({ title: String })

const page = usePage()
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')
const notificationStore = useNotificationStore()

function isActive(name) {
  return route().current()?.startsWith(name)
}

watch(() => page.props.flash, (flash) => {
  const errors = page.props.errors
  if (flash?.success) {
    notificationStore.success(flash.success)
  } else if (flash?.error) {
    notificationStore.error(flash.error)
  } else if (flash?.warning) {
    notificationStore.warning(flash.warning)
  } else if (flash?.info) {
    notificationStore.info(flash.info)
  } else if (errors && Object.keys(errors).length > 0) {
    const msgs = Object.values(errors).flat()
    notificationStore.error(msgs[0] || 'Terdapat kesalahan pada formulir.')
  }
}, { deep: true, immediate: true })
</script>
