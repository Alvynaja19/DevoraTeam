<template>
  <AuthLayout>
    <!-- STEP 1: Email Form -->
    <div v-show="step === 1">
      <div class="mb-8">
        <h1 class="text-gray-900 text-2xl font-bold">Lupa Password</h1>
        <p class="text-gray-500 text-sm mt-1">Masukkan email Anda untuk menerima kode OTP.</p>
      </div>

      <div v-if="$page.props.flash.success && step === 1" class="mb-4 bg-emerald-50 text-emerald-600 p-3 rounded-xl text-sm font-medium border border-emerald-100">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error && step === 1" class="mb-4 bg-red-50 text-red-600 p-3 rounded-xl text-sm font-medium border border-red-100">
        {{ $page.props.flash.error }}
      </div>

      <form @submit.prevent="submitEmail" class="space-y-5">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Email</label>
          <div class="relative">
             <input v-model="emailForm.email" type="email"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50"
              placeholder="admin@sekolah.sch.id" required />
          </div>
          <p v-if="emailForm.errors.email" class="text-red-500 text-xs">{{ emailForm.errors.email }}</p>
        </div>

        <button type="submit" :disabled="emailForm.processing"
          class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60"
          style="background: linear-gradient(135deg, #1a6b5a, #25a07e)">
          <span v-if="emailForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ emailForm.processing ? 'Mengirim...' : 'Kirim Kode OTP' }}
        </button>

        <!-- Back to login (only visible in step 1) -->
        <div class="mt-7 space-y-4">
          <div class="relative flex items-center gap-3">
            <div class="flex-1 h-px bg-gray-100"></div>
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Atau</span>
            <div class="flex-1 h-px bg-gray-100"></div>
          </div>
          <p class="text-center text-sm text-gray-500">
            Ingat password Anda?
            <Link :href="route('login')" class="font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Kembali ke halaman Login</Link>
          </p>
        </div>
      </form>
    </div>

    <!-- STEP 2: OTP Form -->
    <div v-show="step === 2">
      <div class="mb-8 text-center">
        <h1 class="text-gray-900 text-2xl font-bold">Verifikasi OTP</h1>
        <p class="text-gray-500 text-sm mt-1">Masukkan 6 digit kode OTP yang kami kirim ke <b>{{ emailForm.email }}</b></p>
      </div>

      <div v-if="$page.props.flash.success && step === 2 && !$page.props.flash.reset_token" class="mb-4 bg-emerald-50 text-emerald-600 p-3 rounded-xl text-sm font-medium border border-emerald-100">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error && step === 2" class="mb-4 bg-red-50 text-red-600 p-3 rounded-xl text-sm font-medium border border-red-100">
        {{ $page.props.flash.error }}
      </div>

      <form @submit.prevent="submitOtp" class="space-y-6">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase text-center">Kode OTP</label>
          <input v-model="otpForm.otp" type="text" maxlength="6"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-2xl tracking-[0.5em] text-center text-gray-900 font-bold outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50 uppercase"
            placeholder="••••••" required />
          <p v-if="otpForm.errors.otp" class="text-red-500 text-xs text-center">{{ otpForm.errors.otp }}</p>
        </div>

        <button type="submit" :disabled="otpForm.processing"
          class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60"
          style="background: linear-gradient(135deg, #1a6b5a, #25a07e)">
          <span v-if="otpForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ otpForm.processing ? 'Memverifikasi...' : 'Verifikasi OTP' }}
        </button>

        <div class="text-center mt-4">
          <button type="button" @click="step = 1" class="text-sm text-gray-500 hover:text-emerald-600 font-medium transition">Salah memasukkan email?</button>
        </div>
      </form>
    </div>

    <!-- STEP 3: Reset Password Form -->
    <div v-show="step === 3">
      <div class="mb-8">
        <h1 class="text-gray-900 text-2xl font-bold">Buat Password Baru</h1>
        <p class="text-gray-500 text-sm mt-1">Silakan buat passsword baru untuk akun Anda.</p>
      </div>

      <form @submit.prevent="submitReset" class="space-y-5">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Password Baru</label>
          <div class="relative">
             <input v-model="resetForm.password" :type="showPass ? 'text' : 'password'"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50"
              placeholder="••••••••" required />
              <button type="button" @click="showPass = !showPass"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg v-if="showPass" width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <svg v-else width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
          </div>
          <p v-if="resetForm.errors.password" class="text-red-500 text-xs">{{ resetForm.errors.password }}</p>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Konfirmasi Password Baru</label>
          <input v-model="resetForm.password_confirmation" type="password"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 bg-gray-50"
            placeholder="••••••••" required />
          <p v-if="resetForm.errors.password_confirmation" class="text-red-500 text-xs">{{ resetForm.errors.password_confirmation }}</p>
        </div>

        <button type="submit" :disabled="resetForm.processing"
          class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60"
          style="background: linear-gradient(135deg, #1a6b5a, #25a07e)">
          <span v-if="resetForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ resetForm.processing ? 'Menyimpan...' : 'Simpan Password Baru' }}
        </button>
      </form>
    </div>

  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const step = ref(1)
const showPass = ref(false)

const emailForm = useForm({
  email: ''
})

const otpForm = useForm({
  email: '',
  otp: ''
})

const resetForm = useForm({
  email: '',
  reset_token: '',
  password: '',
  password_confirmation: ''
})

function submitEmail() {
  emailForm.post(route('password.email'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      // Transition to OTP step on success
      if (page.props.flash?.success && !Object.keys(emailForm.errors).length) {
        step.value = 2;
        otpForm.email = emailForm.email;
        resetForm.email = emailForm.email;
      }
    }
  })
}

function submitOtp() {
  otpForm.post(route('password.verify'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      // Transition to Update Password step if token is provided
      if (page.props.flash?.reset_token && !Object.keys(otpForm.errors).length) {
        resetForm.reset_token = page.props.flash.reset_token;
        step.value = 3;
      }
    }
  })
}

function submitReset() {
  resetForm.post(route('password.update'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // Usually redirects away.
      resetForm.reset('password', 'password_confirmation');
    }
  })
}
</script>
