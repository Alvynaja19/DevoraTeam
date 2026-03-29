<template>
  <AuthLayout>
    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-gray-900 text-2xl font-bold">Masuk ke Panel Admin</h1>
      <p class="text-gray-500 text-sm mt-1">Masukkan kredensial akun Anda untuk melanjutkan akses</p>
    </div>

    <form @submit.prevent="submit" class="space-y-5">

      <!-- Email -->
      <div class="space-y-1.5">
        <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Email</label>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <input v-model="form.email" type="email"
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50"
            placeholder="admin@sekolah.sch.id" autocomplete="email" />
        </div>
        <p v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</p>
      </div>

      <!-- Password -->
      <div class="space-y-1.5">
        <div class="flex items-center justify-between">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Password</label>
          <Link :href="route('password.request')" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
            Lupa Password?
          </Link>
        </div>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <input v-model="form.password" :type="showPass ? 'text' : 'password'"
            class="w-full pl-10 pr-11 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50"
            placeholder="••••••••" autocomplete="current-password" />
          <button type="button" @click="showPass = !showPass"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
            <svg v-if="showPass" width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg v-else width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <p v-if="form.errors.password" class="text-red-500 text-xs">{{ form.errors.password }}</p>
      </div>

      <!-- Remember me -->
      <label class="flex items-center gap-2.5 cursor-pointer select-none">
        <input v-model="form.remember" type="checkbox"
          class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 cursor-pointer" />
        <span class="text-sm text-gray-500">Ingat saya</span>
      </label>

      <!-- Submit -->
      <button type="submit" :disabled="form.processing"
        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60 disabled:cursor-not-allowed"
        style="background: linear-gradient(135deg, #1a6b5a, #25a07e)">
        <span v-if="form.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ form.processing ? 'Masuk...' : 'Masuk' }}
        <svg v-if="!form.processing" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </form>

    <!-- Divider + Links -->
    <div class="mt-7 space-y-4">
      <div class="relative flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Atau</span>
        <div class="flex-1 h-px bg-gray-100"></div>
      </div>

      <!-- Claim account link -->
      <Link :href="route('claim.show')"
        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold border border-emerald-200 text-emerald-700 hover:bg-emerald-50 transition-all">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Login pertama kali? Aktivasi dengan NIS/NIP
      </Link>

      <p class="text-center text-sm text-gray-500">
        Belum punya akun?
        <Link :href="route('register')" class="font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Daftar sebagai Umum →</Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const showPass = ref(false)
const form = useForm({ email: '', password: '', remember: false })

function submit() {
  form.post(route('login'), { onFinish: () => form.reset('password') })
}
</script>
