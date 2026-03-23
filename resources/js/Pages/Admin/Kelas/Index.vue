<template>
  <AdminLayout title="Data Kelas">
    <template #topbar-actions>
      <button @click="openAdd" class="btn btn-primary">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Tambah Kelas
      </button>
    </template>

    <!-- Flash -->
    <div v-if="$page.props.flash?.success"
      class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center gap-2 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
      {{ $page.props.flash.success }}
    </div>

    <div v-if="$page.props.flash?.error"
      class="mb-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 text-sm font-medium flex items-center gap-2 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
      {{ $page.props.flash.error }}
    </div>

    <!-- Filter & Search (separate card) -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
      <!-- Left: Rows per page -->
      <div class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
        <span>Tampilkan</span>
        <select v-model="perPage" @change="applySearch"
          class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <span>baris</span>
      </div>

      <!-- Right: Search -->
      <div class="relative w-full md:w-96">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input v-model="search" type="text" placeholder="Cari nama kelas..."
          class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
      </div>
    </div>

    <!-- Table (separate card) -->
    <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm mt-4">
      <table class="w-full min-w-[700px]">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">No</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Nama Kelas</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Tingkat</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Jurusan</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Anggota</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Status</th>
            <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-if="classes.data && classes.data.length > 0" v-for="(kelas, i) in classes.data" :key="kelas.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900">{{ (classes.from || 1) + i }}</td>
            <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-semibold text-gray-900">{{ kelas.name }}</td>
            <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-600">{{ kelas.grade || '—' }}</td>
            <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-600">{{ kelas.major || '—' }}</td>
            <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-semibold text-gray-700">{{ kelas.members_count }}</td>
            <td class="px-3 md:px-6 py-3 md:py-4">
              <span class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="kelas.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                {{ kelas.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="px-3 md:px-6 py-3 md:py-4">
              <div class="flex gap-2">
                <button @click="openEdit(kelas)" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-700 font-medium">Edit</button>
                <button @click="confirmDelete(kelas)" class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-medium">Hapus</button>
              </div>
            </td>
          </tr>
          <!-- Empty state -->
          <tr v-if="!classes.data || classes.data.length === 0">
            <td colspan="7" class="px-6 py-16 text-center">
              <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 text-gray-300"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <div class="text-gray-500 font-medium">Belum ada data kelas</div>
              <p class="text-sm text-gray-400 mt-1">Klik "Tambah Kelas" untuk menambahkan kelas pertama</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination (standalone, outside table card) -->
    <div v-if="classes.data && classes.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
      <div class="text-sm text-gray-500">
        Menampilkan <span class="font-semibold text-gray-800">{{ classes.from || 0 }}</span> sampai <span class="font-semibold text-gray-800">{{ classes.to || 0 }}</span> dari <span class="font-semibold text-gray-800">{{ classes.total }}</span> data
      </div>
      <div v-if="classes.last_page > 1" class="flex gap-1.5 items-center">
        <template v-for="(link, idx) in classes.links" :key="idx">
          <Link v-if="link.url" :href="link.url"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
            :class="link.active ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
            v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)" />
          <span v-else class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 text-sm border border-gray-200" v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)"></span>
        </template>
      </div>
    </div>


    <!-- ── Add / Edit Modal ────────────────────────── -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden dark:bg-slate-800">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
          <h3 class="font-semibold text-lg text-slate-800 dark:text-white">
            {{ editingKelas ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div class="form-group">
            <label class="form-label dark:text-slate-300">Nama Kelas <span class="text-rose-500">*</span></label>
            <input v-model="form.name" type="text" class="form-input" placeholder="Contoh: X IPA 1" required />
            <span v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
              <label class="form-label dark:text-slate-300">Tingkat</label>
              <input v-model="form.grade" type="text" class="form-input" placeholder="X, XI, XII" />
            </div>
            <div class="form-group">
              <label class="form-label dark:text-slate-300">Jurusan</label>
              <input v-model="form.major" type="text" class="form-input" placeholder="IPA, IPS, dll" />
            </div>
          </div>
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input v-model="form.is_active" type="checkbox"
              class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
            <span class="text-sm text-slate-600 dark:text-slate-300">Kelas aktif (tampil di form registrasi)</span>
          </label>
          <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="closeModal" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : (editingKelas ? 'Simpan Perubahan' : 'Tambah Kelas') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ── Delete Confirm Modal ────────────────────── -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 dark:bg-slate-800">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f43f5e" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
          </div>
          <div>
            <h3 class="font-semibold text-slate-800 dark:text-white">Hapus Kelas</h3>
            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
          </div>
        </div>
        <p class="text-sm text-slate-600 dark:text-slate-300 mb-5">
          Yakin ingin menghapus kelas <strong>"{{ deleteTarget.name }}"</strong>?
          <span v-if="deleteTarget.members_count > 0" class="text-rose-500 block mt-1">
            ⚠ Kelas ini memiliki {{ deleteTarget.members_count }} anggota. Hapus anggota terlebih dahulu.
          </span>
        </p>
        <div class="flex justify-end gap-3">
          <button @click="deleteTarget = null" class="btn btn-secondary">Batal</button>
          <button @click="doDelete" class="btn bg-rose-600 hover:bg-rose-700 text-white">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({
  classes: Object,
  filters: Object,
})

// Search & Pagination
const search = ref(props.filters?.search || '')
const perPage = ref(props.filters?.per_page || '10')

function applySearch() {
  router.get(route('kelas.index'), {
    search: search.value || undefined,
    per_page: perPage.value,
  }, { preserveState: true, replace: true })
}

let searchTimer = null
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => applySearch(), 400)
})

// Modal
const showModal    = ref(false)
const editingKelas = ref(null)

const form = useForm({
  name: '', grade: '', major: '', is_active: true,
})

function openAdd() {
  editingKelas.value = null
  form.reset()
  form.is_active = true
  showModal.value = true
}

function openEdit(kelas) {
  editingKelas.value = kelas
  form.name      = kelas.name
  form.grade     = kelas.grade || ''
  form.major     = kelas.major || ''
  form.is_active = kelas.is_active
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingKelas.value = null
  form.reset()
}

function submitForm() {
  if (editingKelas.value) {
    form.put(route('kelas.update', editingKelas.value.id), { onSuccess: closeModal })
  } else {
    form.post(route('kelas.store'), { onSuccess: closeModal })
  }
}

// Delete
const deleteTarget = ref(null)
function confirmDelete(kelas) { deleteTarget.value = kelas }
function doDelete() {
  router.delete(route('kelas.destroy', deleteTarget.value.id), {
    onSuccess: () => { deleteTarget.value = null }
  })
}
</script>
