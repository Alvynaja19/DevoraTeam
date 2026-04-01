<template>
  <!-- Full screen split layout -->
  <div class="min-h-screen flex font-[Outfit,sans-serif]">

    <!-- ─── LEFT PANEL — Hijau gelap ────────────────────── -->
    <div class="hidden lg:flex lg:w-[460px] flex-col justify-between p-10 relative overflow-hidden"
      style="background: linear-gradient(160deg, #1a3d2e 0%, #1e5c42 50%, #165c3a 100%)">

      <!-- Decorative circles -->
      <div class="absolute -top-20 -left-20 w-64 h-64 rounded-full opacity-10"
        style="background: radial-gradient(circle, #4ade80, transparent)"></div>
      <div class="absolute bottom-20 -right-12 w-48 h-48 rounded-full opacity-10"
        style="background: radial-gradient(circle, #86efac, transparent)"></div>

      <!-- Top: Logo + Nama Sekolah -->
      <div class="relative z-10">
        <div class="flex flex-col items-start gap-4">
          <!-- Icon logo -->
          <div class="w-20 h-20 rounded-2xl flex items-center justify-center p-2"
            style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.15)">
            <img src="/images/logo-sman4-jember.png" alt="Logo SMAN 4" class="w-full h-full object-contain drop-shadow-xl" />
          </div>
          <div>
            <h2 class="text-white text-2xl font-bold leading-tight">Sistem Perpustakaan</h2>
            <p class="text-emerald-300/70 text-sm mt-0.5">Scholastic Atelier Academy</p>
          </div>
        </div>

        <p class="text-white/70 text-base leading-relaxed mt-8 max-w-xs">
          Kelola perpustakaan sekolah dengan<br>mudah dan efisien
        </p>
      </div>

      <!-- Middle: Ilustrasi buku -->
      <div class="relative z-10 flex-1 flex items-center justify-center py-8">
        <div class="w-72 h-64 rounded-2xl overflow-hidden relative"
          style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1)">
          <!-- SVG ilustrasi tumpukan buku -->
          <svg class="absolute inset-0 w-full h-full" viewBox="0 0 288 256" fill="none">
            <!-- Meja -->
            <rect x="40" y="210" width="208" height="8" rx="4" fill="rgba(255,255,255,0.08)"/>
            <!-- Buku 1 (paling bawah, lebar) -->
            <rect x="60" y="185" width="168" height="26" rx="5" fill="#2d6a4f" opacity="0.9"/>
            <rect x="60" y="185" width="14" height="26" rx="3" fill="#1b4332" opacity="0.9"/>
            <!-- Buku 2 -->
            <rect x="72" y="160" width="148" height="26" rx="5" fill="#40916c" opacity="0.85"/>
            <rect x="72" y="160" width="14" height="26" rx="3" fill="#2d6a4f" opacity="0.9"/>
            <!-- Buku 3 -->
            <rect x="80" y="135" width="136" height="26" rx="5" fill="#52b788" opacity="0.8"/>
            <rect x="80" y="135" width="14" height="26" rx="3" fill="#40916c" opacity="0.9"/>
            <!-- Buku 4 -->
            <rect x="90" y="110" width="120" height="26" rx="5" fill="#74c69d" opacity="0.7"/>
            <rect x="90" y="110" width="14" height="26" rx="3" fill="#52b788" opacity="0.9"/>
            <!-- Buku 5 (paling atas) -->
            <rect x="100" y="85" width="100" height="26" rx="5" fill="#95d5b2" opacity="0.6"/>
            <rect x="100" y="85" width="14" height="26" rx="3" fill="#74c69d" opacity="0.9"/>
            <!-- Line detail buku -->
            <line x1="100" y1="95" x2="190" y2="95" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
            <line x1="90" y1="120" x2="200" y2="120" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
            <line x1="80" y1="145" x2="206" y2="145" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
          </svg>
        </div>
      </div>

      <!-- Bottom: Version -->
      <div class="relative z-10">
        <p class="text-emerald-400/40 text-xs font-mono">v2.5 — 2025</p>
      </div>
    </div>

    <!-- ─── RIGHT PANEL — Putih, form ───────────────────── -->
    <div class="flex-1 flex flex-col bg-white">
      <!-- Help button top right -->
      <div class="flex justify-end p-6">
        <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 text-sm font-bold transition-colors">
          ?
        </button>
      </div>

      <!-- Form area centered -->
      <div class="flex-1 flex items-center justify-center px-8 pb-8">
        <div class="w-full max-w-sm">
          <slot />
        </div>
      </div>

      <!-- Footer -->
      <div class="px-8 py-5 text-center border-t border-gray-100">
        <p class="text-gray-400 text-xs">© {{ new Date().getFullYear() }} Perpustakaan SMA — Hak Cipta Dilindungi</p>
      </div>
    </div>

  </div>

  <ToastNotification />
</template>

<script setup>
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ToastNotification from '@/Components/ToastNotification.vue'
import { useNotificationStore } from '@/stores/notification'

const page = usePage()
const notificationStore = useNotificationStore()

watch(() => page.props.flash, (flash) => {
  if (flash?.success) notificationStore.success(flash.success)
  else if (flash?.error) notificationStore.error(flash.error)
  else if (flash?.warning) notificationStore.warning(flash.warning)
  else if (flash?.info) notificationStore.info(flash.info)
}, { deep: true, immediate: true })
</script>
