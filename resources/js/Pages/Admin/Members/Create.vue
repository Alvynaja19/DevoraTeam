<template>
  <AdminLayout title="Tambah Anggota Manual">
    <div class="max-w-3xl mx-auto">
      <div class="flex items-center gap-3 mb-6">
        <Link :href="route('members.index')" class="btn btn-secondary py-1.5 px-3">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M15 19.5 7.5 12 15 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </Link>
        <div>
          <h1 class="text-xl font-bold text-slate-800">Tambah Anggota</h1>
          <p class="text-sm text-slate-500 mt-0.5">Pilih tipe anggota dan lengkapi data di bawah</p>
        </div>
      </div>

      <div class="card p-6">
        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Tipe Anggota -->
          <div class="space-y-1.5">
            <label class="form-label">Tipe Anggota <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.type" value="siswa" class="text-primary-600 focus:ring-primary-500" />
                <span class="text-sm font-medium text-slate-700">Siswa</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.type" value="guru" class="text-primary-600 focus:ring-primary-500" />
                <span class="text-sm font-medium text-slate-700">Guru / Karyawan</span>
              </label>
            </div>
          </div>

          <!-- Basic Data -->
          <div class="grid grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="form-input" required placeholder="Contoh: Ahmad Firdaus" />
              <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
            </div>
            <div class="space-y-1.5">
              <label class="form-label">{{ form.type === 'siswa' ? 'NIS' : 'NIP' }} <span class="text-red-500">*</span></label>
              <input v-model="form.nis_nip" type="text" class="form-input" required :placeholder="form.type === 'siswa' ? '12345' : '19800101...'" />
              <p v-if="form.errors.nis_nip" class="text-xs text-red-500">{{ form.errors.nis_nip }}</p>
            </div>
          </div>

          <!-- Conditional Siswa vs Guru -->
          <div v-if="form.type === 'siswa'" class="grid grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="form-label">Kelas <span class="text-red-500">*</span></label>
              <select v-model="form.class_id" class="form-input" required>
                <option value="">Pilih Kelas</option>
                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <p v-if="form.errors.class_id" class="text-xs text-red-500">{{ form.errors.class_id }}</p>
            </div>
            <div class="space-y-1.5">
              <label class="form-label">NISN</label>
              <input v-model="form.nisn" type="text" class="form-input" placeholder="00xxxxxxxx" />
            </div>
          </div>

          <div v-if="form.type === 'guru'" class="space-y-1.5">
            <label class="form-label">Pangkat / Golongan Ruang</label>
            <input v-model="form.pangkat_golongan" type="text" class="form-input" placeholder="Pembina / IV a" />
          </div>

          <!-- Umum Detail -->
          <div class="grid grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="form-label">NIK</label>
              <input v-model="form.nik" type="text" class="form-input" placeholder="32xxxxxxxxxxx" />
            </div>
            <div class="space-y-1.5">
              <label class="form-label">Agama</label>
              <select v-model="form.agama" class="form-input">
                <option value="">Pilih</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
              </select>
            </div>
            
            <div class="space-y-1.5">
              <label class="form-label">Tempat Lahir</label>
              <input v-model="form.tempat_lahir" type="text" class="form-input" placeholder="Jakarta" />
            </div>
            <div class="space-y-1.5">
              <label class="form-label">Tanggal Lahir</label>
              <input v-model="form.tanggal_lahir" type="date" class="form-input" />
            </div>

            <div class="space-y-1.5">
              <label class="form-label">Jenis Kelamin</label>
              <select v-model="form.jenis_kelamin" class="form-input">
                <option value="">Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="space-y-1.5">
              <label class="form-label">No. HP</label>
              <input v-model="form.phone" type="tel" class="form-input" placeholder="08xxxxxxxx" />
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="form-label">Alamat Lengkap</label>
            <textarea v-model="form.address" class="form-input" rows="2" placeholder="Jl. Raya ..."></textarea>
          </div>

          <div class="pt-4 flex justify-end">
            <button type="submit" :disabled="form.processing" class="btn btn-primary px-8">
              <span v-if="form.processing" class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin inline-block align-middle"></span>
              {{ form.processing ? 'Menyimpan...' : 'Simpan Anggota' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({
  classes: Array,
})

const form = useForm({
  type: 'siswa',
  name: '',
  nis_nip: '',
  class_id: '',
  nisn: '',
  pangkat_golongan: '',
  nik: '',
  agama: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  phone: '',
  address: '',
})

function submit() {
  form.post(route('members.store'), {
    preserveState: true,
  })
}
</script>
