<template>
  <AuthLayout>
    <!-- Back link + Title -->
    <div class="mb-7">
      <Link :href="route('home')"
        class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 transition-colors mb-5">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
          <path d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali ke Beranda
      </Link>
      <h1 class="text-gray-900 text-2xl font-bold">Daftar Anggota</h1>
      <p class="text-gray-500 text-sm mt-1">Buat akun perpustakaan sebagai anggota umum</p>
    </div>

    <form @submit.prevent="submit" class="space-y-4">

      <!-- Nama -->
      <div class="space-y-1.5">
        <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Nama Lengkap <span class="text-red-400">*</span></label>
        <input v-model="form.name" type="text"
          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
          placeholder="Ahmad Firdaus" autocomplete="name" />
        <p v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</p>
      </div>

      <!-- Email + HP -->
      <div class="grid grid-cols-2 gap-3">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Email <span class="text-red-400">*</span></label>
          <input v-model="form.email" type="email"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
            placeholder="email@contoh.com" autocomplete="email" />
          <p v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</p>
        </div>
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">No. HP</label>
          <input v-model="form.phone" type="tel"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
            placeholder="08xxxxxxxxxx" />
        </div>
      </div>

      <!-- Password -->
      <div class="grid grid-cols-2 gap-3">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Password <span class="text-red-400">*</span></label>
          <div class="relative">
            <input v-model="form.password" :type="showPass ? 'text' : 'password'"
              class="w-full pl-4 pr-9 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
              placeholder="Min. 6 karakter" autocomplete="new-password" />
            <button type="button" @click="showPass = !showPass"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
              <svg v-if="showPass" width="14" height="14" fill="none" viewBox="0 0 24 24">
                <path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <svg v-else width="14" height="14" fill="none" viewBox="0 0 24 24">
                <path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
          <p v-if="form.errors.password" class="text-red-500 text-xs">{{ form.errors.password }}</p>
        </div>
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Konfirmasi <span class="text-red-400">*</span></label>
          <input v-model="form.password_confirmation" :type="showPass ? 'text' : 'password'"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
            placeholder="Ulangi password" autocomplete="new-password" />
        </div>
      </div>


      <!-- Submit -->
      <button type="submit" :disabled="form.processing"
        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60 disabled:cursor-not-allowed mt-1"
        style="background: linear-gradient(135deg, #1a6b5a, #25a07e)">
        <span v-if="form.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ form.processing ? 'Mendaftar...' : 'Daftar Sekarang' }}
        <svg v-if="!form.processing" width="15" height="15" fill="none" viewBox="0 0 24 24">
          <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </form>

    <!-- Divider + Links -->
    <div class="mt-6 space-y-3">
      <div class="relative flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Atau</span>
        <div class="flex-1 h-px bg-gray-100"></div>
      </div>
      <p class="text-center text-sm text-gray-500">
        Siswa / Guru?
        <Link :href="route('claim.show')" class="font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Aktivasi akun dengan NIS/NIP →</Link>
      </p>
      <p class="text-center text-sm text-gray-500">
        Sudah punya akun?
        <Link :href="route('login')" class="font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Masuk →</Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const showPass = ref(false)

const form = useForm({
  name: '', email: '', password: '',
  password_confirmation: '', phone: '',
})

function submit() {
  form.post(route('register'))
}
</script>
