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
    <Teleport to="body">
      <Transition name="toast">
        <div v-if="toast.show" class="fixed top-6 right-6 z-50 flex flex-col gap-3 min-w-[300px] pointer-events-none">
          <!-- Success Toast -->
          <div v-if="toast.type === 'success'" class="bg-white border-l-4 border-emerald-500 shadow-xl rounded-xl p-4 flex items-start gap-4 pointer-events-auto">
            <div class="rounded-full bg-emerald-100 p-1 flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-bold text-gray-900">Berhasil!</h4>
              <p class="text-sm text-gray-500 mt-1">{{ toast.message }}</p>
            </div>
            <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Error Toast -->
          <div v-if="toast.type === 'error'" class="bg-white border-l-4 border-rose-500 shadow-xl rounded-xl p-4 flex items-start gap-4 pointer-events-auto">
            <div class="rounded-full bg-rose-100 p-1 flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-bold text-gray-900">Oops, Gagal!</h4>
              <p class="text-sm text-gray-500 mt-1">{{ toast.message }}</p>
              <ul v-if="toast.errors && Object.keys(toast.errors).length > 0" class="list-disc pl-4 mt-2 text-xs text-rose-600">
                <li v-for="(err, idx) in toast.errors" :key="idx">{{ err }}</li>
              </ul>
            </div>
            <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(100px) translateY(-10px);
}
</style>

<script setup>
import { computed, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavItem from '@/Components/NavItem.vue'
import IconDashboard from '@/Components/Icons/IconDashboard.vue'
import IconUsers from '@/Components/Icons/IconUsers.vue'
import IconBook from '@/Components/Icons/IconBook.vue'
import IconLoan from '@/Components/Icons/IconLoan.vue'
import IconReturn from '@/Components/Icons/IconReturn.vue'
import IconFine from '@/Components/Icons/IconFine.vue'
import IconSettings from '@/Components/Icons/IconSettings.vue'

defineProps({ title: String })

const page = usePage()
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')

function isActive(name) {
  return route().current()?.startsWith(name)
}

// Toast System
const toast = ref({
  show: false,
  type: '', // success or error
  message: '',
  errors: {}
})

// Watch for Inertia flash messages and display them as toasts
watch(() => [page.props.flash, page.props.errors], ([flash, errors]) => {
  let hasFlash = false
  
  if (flash?.success) {
    toast.value = { show: true, type: 'success', message: flash.success, errors: {} }
    hasFlash = true
  } else if (flash?.error || (errors && Object.keys(errors).length > 0)) {
    toast.value = { 
      show: true, 
      type: 'error', 
      message: flash?.error || 'Terdapat kesalahan pada formulir.', 
      errors: errors || {} 
    }
    hasFlash = true
  }

  // Auto hide after 4 seconds
  if (hasFlash) {
    setTimeout(() => {
      toast.value.show = false
    }, 4000)
  }
}, { deep: true, immediate: true })
</script>
