<template>
  <div class="public-layout">
    <!-- ── NAV ── -->
    <Disclosure as="nav"
      class="fixed top-0 left-0 right-0 z-50 bg-[#faf8f3]/90 backdrop-blur-lg border-b border-[#d8f3e3] animate-[fadeDown_0.7s_ease_both]"
      v-slot="{ open }">
      <div class="mx-auto max-w-[1200px] px-[5vw]">
        <div class="relative flex h-16 items-center justify-between">

          <!-- Mobile hamburger -->
          <div class="absolute left-0 top-1/2 -translate-y-1/2 sm:hidden">
            <DisclosureButton
              class="inline-flex items-center justify-center rounded-lg p-2 text-[#4a5250] hover:bg-[#f0faf4] hover:text-[#1a1f1c] transition-colors focus:outline-none">
              <span class="sr-only">Buka menu</span>
              <Bars3Icon v-if="!open" class="block h-6 w-6" />
              <XMarkIcon v-else class="block h-6 w-6" />
            </DisclosureButton>
          </div>

          <!-- Logo + Nav Links -->
          <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
            <Link :href="route('home')" class="flex shrink-0 items-baseline gap-1.5 no-underline">
              <span class="font-[500] text-[1.45rem] text-[#1a1f1c] tracking-[0.01em]" style="font-family: 'Cormorant Garamond', Georgia, serif">SMA NEGERI 4 JEMBER</span>
              <!-- <span class="hidden sm:inline text-[0.72rem] text-[#8a9490] tracking-[0.04em]">Perpustakaan</span> -->
            </Link>

            <!-- Desktop nav -->
            <div class="hidden sm:ml-8 sm:flex sm:items-center sm:gap-1">
              <Link v-for="item in navigation" :key="item.name"
                :href="item.href"
                :class="[
                  isActivePage(item.key)
                    ? 'text-[#1f7a4a] font-medium bg-[#f0faf4]'
                    : 'text-[#4a5250] hover:text-[#2d9e64]',
                  'text-sm px-3 py-1.5 rounded-lg no-underline tracking-[0.03em] transition-colors duration-200'
                ]">
                {{ item.name }}
              </Link>
            </div>
          </div>

          <!-- Right: auth -->
          <div class="flex items-center gap-2">
            <template v-if="$page.props.auth?.user">
              <Menu as="div" class="relative">
                <MenuButton
                  class="flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm bg-transparent border-none cursor-pointer hover:bg-[#f0faf4] transition-colors">
                  <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-xs font-bold text-white bg-[#2d9e64] shrink-0">
                    {{ $page.props.auth.user.name[0].toUpperCase() }}
                  </div>
                  <span class="hidden sm:block font-medium text-[#1a1f1c]">{{ $page.props.auth.user.name.split(' ')[0] }}</span>
                  <ChevronDownIcon class="hidden sm:block h-4 w-4 text-[#8a9490]" />
                </MenuButton>

                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95">
                  <MenuItems
                    class="absolute right-0 z-10 mt-2 w-[200px] rounded-xl bg-white border border-[#d8f3e3] shadow-xl overflow-hidden outline-none">
                    <div class="px-3 py-2 border-b border-[#d8f3e3]">
                      <p class="text-xs text-[#8a9490]">Masuk sebagai</p>
                      <p class="text-sm font-semibold text-[#1a1f1c] truncate">{{ $page.props.auth.user.name }}</p>
                    </div>
                    <MenuItem v-slot="{ active }">
                      <Link :href="route('catalog')"
                        :class="[active ? 'bg-[#f0faf4]' : '', 'flex items-center gap-2 px-4 py-2.5 text-sm text-[#4a5250] transition-colors']">
                        📚 Jelajahi Katalog
                      </Link>
                    </MenuItem>
                    <div class="border-t border-[#d8f3e3]">
                      <MenuItem v-slot="{ active }">
                        <Link :href="route('logout')" method="post" as="button"
                          :class="[active ? 'bg-red-50' : '', 'flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-500 transition-colors']">
                          🚪 Keluar
                        </Link>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </template>

            <template v-else>
              <Link :href="route('login')"
                class="hidden sm:inline-flex px-4 py-1.5 rounded-full text-sm text-[#4a5250] no-underline border border-[#b2e6c7] hover:border-[#4aba82] hover:text-[#1f7a4a] transition-all duration-200">
                Masuk
              </Link>
              <Link :href="route('register')"
                class="inline-flex px-5 py-1.5 rounded-full text-sm font-medium text-white no-underline bg-[#2d9e64] hover:bg-[#1f7a4a] hover:-translate-y-px transition-all duration-200">
                Daftar
              </Link>
            </template>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <DisclosurePanel class="sm:hidden border-t border-[#d8f3e3]">
        <div class="space-y-1 px-3 py-3">
          <DisclosureButton v-for="item in navigation" :key="item.name"
            as="a" :href="item.href"
            :class="[
              isActivePage(item.key)
                ? 'bg-[#f0faf4] text-[#1f7a4a] font-medium'
                : 'text-[#4a5250] hover:bg-[#f0faf4] hover:text-[#1a1f1c]',
              'flex items-center rounded-lg px-3 py-2.5 text-base no-underline transition-colors'
            ]">
            {{ item.name }}
          </DisclosureButton>
        </div>
        <div v-if="!$page.props.auth?.user" class="px-3 py-3 border-t border-[#d8f3e3] flex gap-2">
          <Link :href="route('login')"
            class="flex-1 text-center py-2 rounded-full text-sm font-medium text-[#4a5250] border border-[#b2e6c7] no-underline">
            Masuk
          </Link>
          <Link :href="route('register')"
            class="flex-1 text-center py-2 rounded-full text-sm font-semibold text-white bg-[#2d9e64] no-underline">
            Daftar
          </Link>
        </div>
      </DisclosurePanel>
    </Disclosure>

    <!-- Page Content -->
    <main>
      <slot />
    </main>

    <!-- ── FOOTER ── -->
    <footer class="site-footer">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="footer-logo">SMA NEGERI 4 JEMBER</div>
          <p class="footer-brand-desc">
            Perpustakaan digital sekolah yang menghadirkan pengetahuan untuk mendukung kegiatan belajar mengajar.
          </p>
        </div>
        <div class="footer-col">
          <h4>Tautan</h4>
          <ul>
            <li><Link :href="route('catalog')">Katalog Buku</Link></li>
            <li><Link :href="route('register')">Daftar Anggota</Link></li>
            <li><Link :href="route('login')">Masuk</Link></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Kontak</h4>
          <ul>
            <li>Jl. Pendidikan No. 45</li>
            <li>perpustakaan@sma.sch.id</li>
            <li>(021) 4567-9032</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© {{ new Date().getFullYear() }} SMA NEGERI 4 JEMBER. Hak cipta dilindungi.</span>
        <span class="footer-bottom-sub">Dibuat dengan ❤️ untuk kemajuan pendidikan</span>
      </div>
    </footer>
  </div>

  <ToastNotification />
</template>

<script setup>
import { computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Disclosure, DisclosureButton, DisclosurePanel,
  Menu, MenuButton, MenuItem, MenuItems,
} from '@headlessui/vue'
import {
  Bars3Icon, XMarkIcon, ChevronDownIcon,
} from '@heroicons/vue/24/outline'
import ToastNotification from '@/Components/ToastNotification.vue'
import { useNotificationStore } from '@/stores/notification'

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)
const notificationStore = useNotificationStore()

watch(() => page.props.flash, (flash) => {
  if (flash?.success) notificationStore.success(flash.success)
  else if (flash?.error) notificationStore.error(flash.error)
  else if (flash?.warning) notificationStore.warning(flash.warning)
  else if (flash?.info) notificationStore.info(flash.info)
}, { deep: true, immediate: true })

const navigation = computed(() => {
  const base = [
    { name: 'Beranda', href: route('home'),    key: 'home'    },
    { name: 'Katalog', href: route('catalog'), key: 'catalog' },
  ]
  if (isLoggedIn.value) {
    try {
      base.push({ name: 'Profil Saya', href: route('anggota.profile'), key: 'anggota' })
    } catch (_) {
      base.push({ name: 'Profil Saya', href: '/anggota/profile', key: 'anggota' })
    }
  }
  return base
})

function isActivePage(key) {
  return route().current()?.startsWith(key)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap');

.public-layout {
  --green-50:  #f0faf4;
  --green-100: #d8f3e3;
  --green-200: #b2e6c7;
  --green-300: #7dd3a8;
  --green-400: #4aba82;
  --green-500: #2d9e64;
  --green-600: #1f7a4a;
  --cream:     #faf8f3;
  --ink:       #1a1f1c;
  --ink-soft:  #4a5250;
  --ink-muted: #8a9490;
  --serif:     'Cormorant Garamond', Georgia, serif;
  --sans:      'Outfit', 'DM Sans', sans-serif;

  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--cream);
  font-family: var(--sans);
  color: var(--ink);
}

@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-20px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── FOOTER ── */
.site-footer {
  background: var(--ink);
  padding: 4rem 5vw 2.5rem;
  color: rgba(255, 255, 255, .6);
  margin-top: auto;
}

.footer-top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
  max-width: 1100px;
  margin: 0 auto 3rem;
}

.footer-logo {
  font-family: var(--serif);
  font-size: 1.6rem;
  font-weight: 500;
  color: #fff;
  margin-bottom: 1rem;
}

.footer-brand-desc {
  font-size: .875rem;
  line-height: 1.7;
  color: rgba(255, 255, 255, .45);
  max-width: 30ch;
}

.footer-col h4 {
  font-size: .8rem;
  font-weight: 500;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, .35);
  margin-bottom: 1rem;
}

.footer-col ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: .65rem;
  font-size: .875rem;
}

.footer-col a {
  color: rgba(255, 255, 255, .55);
  text-decoration: none;
  transition: color .25s;
}

.footer-col a:hover {
  color: var(--green-300);
}

.footer-col li:not(:has(a)) {
  color: rgba(255, 255, 255, .4);
  font-size: .85rem;
}

.footer-bottom {
  max-width: 1100px;
  margin: 0 auto;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, .08);
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: .8rem;
  flex-wrap: wrap;
  gap: 1rem;
  color: rgba(255, 255, 255, .3);
}

.footer-bottom-sub {
  font-size: .78rem;
}

@media (max-width: 768px) {
  .footer-top { grid-template-columns: 1fr; gap: 2rem; }
}
</style>
