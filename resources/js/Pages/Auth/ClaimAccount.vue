<template>
  <AuthLayout>
    <div class="mb-7">
      <Link :href="route('login')"
        class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 transition-colors mb-5">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
          <path d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali ke Login
      </Link>
      <h1 class="text-gray-900 text-2xl font-bold">Aktivasi Akun</h1>
      <p class="text-gray-500 text-sm mt-1">Masukkan NIS (siswa) atau NIP (guru) untuk mengaktifkan akun perpustakaan</p>
    </div>

    <!-- Step 1: Lookup NIS/NIP -->
    <div v-if="!foundMember">
      <form @submit.prevent="submitLookup" class="space-y-4">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">NIS / NIP <span class="text-red-400">*</span></label>
          <input v-model="lookupForm.nis_nip" type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
            placeholder="Masukkan NIS atau NIP Anda" autofocus />
          <p v-if="lookupForm.errors.nis_nip" class="text-red-500 text-xs">{{ lookupForm.errors.nis_nip }}</p>
        </div>

        <div class="flex items-start gap-2.5 p-3 rounded-xl text-xs text-blue-800 bg-blue-50 border border-blue-100">
          <svg class="flex-shrink-0 mt-0.5" width="13" height="13" fill="none" viewBox="0 0 24 24">
            <path d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Data NIS/NIP sudah diinput oleh admin. Jika tidak ditemukan, hubungi petugas perpustakaan.</span>
        </div>

        <button type="submit" :disabled="lookupForm.processing"
          class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60 disabled:cursor-not-allowed"
          style="background: linear-gradient(135deg, #1e293b, #334155)">
          <span v-if="lookupForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ lookupForm.processing ? 'Mencari...' : 'Cari Data Saya' }}
        </button>
      </form>
    </div>

    <!-- Step 2: Set Email & Password -->
    <div v-else>
      <!-- Found member card -->
      <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 mb-5">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-sm">
            {{ foundMember.name[0].toUpperCase() }}
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-900">{{ foundMember.name }}</div>
            <div class="text-xs text-gray-500">
              {{ foundMember.type === 'siswa' ? 'Siswa' : 'Guru / Karyawan' }} · {{ foundMember.nis_nip }}
            </div>
          </div>
          <button @click="resetLookup" class="ml-auto text-xs text-gray-400 hover:text-gray-600 transition-colors">
            Ganti
          </button>
        </div>
      </div>

      <form @submit.prevent="submitActivate" class="space-y-4">
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Email <span class="text-red-400">*</span></label>
          <input v-model="activateForm.email" type="email"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
            placeholder="email@contoh.com" autofocus />
          <p v-if="activateForm.errors.email" class="text-red-500 text-xs">{{ activateForm.errors.email }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Password <span class="text-red-400">*</span></label>
            <input v-model="activateForm.password" :type="showPass ? 'text' : 'password'"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
              placeholder="Min. 6 karakter" />
            <p v-if="activateForm.errors.password" class="text-red-500 text-xs">{{ activateForm.errors.password }}</p>
          </div>
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold tracking-widest text-gray-500 uppercase">Konfirmasi <span class="text-red-400">*</span></label>
            <input v-model="activateForm.password_confirmation" :type="showPass ? 'text' : 'password'"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 bg-gray-50 outline-none transition-all focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10"
              placeholder="Ulangi password" />
          </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-500">
          <input type="checkbox" v-model="showPass" class="accent-emerald-500" />
          Tampilkan password
        </label>

        <button type="submit" :disabled="activateForm.processing"
          class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white text-sm font-bold tracking-wide transition-all disabled:opacity-60 disabled:cursor-not-allowed"
          style="background: linear-gradient(135deg, #059669, #10b981)">
          <span v-if="activateForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ activateForm.processing ? 'Mengaktifkan...' : 'Aktivasi Akun' }}
          <svg v-if="!activateForm.processing" width="15" height="15" fill="none" viewBox="0 0 24 24">
            <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </form>
    </div>

    <!-- Footer -->
    <div class="mt-6 space-y-4">
      <div class="relative flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Atau</span>
        <div class="flex-1 h-px bg-gray-100"></div>
      </div>
      <p class="text-center text-sm text-gray-500">
        Bukan siswa/guru?
        <Link :href="route('register')" class="font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">Daftar sebagai Umum →</Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
  foundMember: { type: Object, default: null },
})

const showPass = ref(false)

const lookupForm = useForm({ nis_nip: '' })
const activateForm = useForm({
  member_id: '',
  email: '',
  password: '',
  password_confirmation: '',
})

// Watch for foundMember to set member_id
if (props.foundMember) {
  activateForm.member_id = props.foundMember.id
}

function submitLookup() {
  lookupForm.post(route('claim.lookup'))
}

function submitActivate() {
  activateForm.member_id = props.foundMember.id
  activateForm.post(route('claim.activate'))
}

function resetLookup() {
  router.get(route('claim.show'))
}
</script>
