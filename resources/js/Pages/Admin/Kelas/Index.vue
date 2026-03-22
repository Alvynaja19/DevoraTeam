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

    <!-- Search -->
    <div class="card px-4 py-3 mb-4">
      <div class="flex items-center gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest shrink-0">Filter</span>
        <div class="relative flex-1">
          <input v-model="search" @keyup.enter="applySearch" type="text"
            placeholder="🔍 Cari nama kelas..." class="form-input px-3 py-1.5 text-sm w-full" />
        </div>
        <button v-if="search" @click="search = ''; applySearch()" class="text-xs text-slate-400 hover:text-slate-600">Hapus</button>
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden" v-if="classes.data.length > 0">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-700">
              <th class="py-3 px-4 font-medium w-12 text-center">No</th>
              <th class="py-3 px-4 font-medium">Nama Kelas</th>
              <th class="py-3 px-4 font-medium">Tingkat</th>
              <th class="py-3 px-4 font-medium">Jurusan</th>
              <th class="py-3 px-4 font-medium text-center">Anggota</th>
              <th class="py-3 px-4 font-medium text-center">Status</th>
              <th class="py-3 px-4 font-medium text-center w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr v-for="(kelas, i) in classes.data" :key="kelas.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
              <td class="py-3 px-4 text-center text-sm text-slate-500">{{ classes.from + i }}</td>
              <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">{{ kelas.name }}</td>
              <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-300">{{ kelas.grade || '—' }}</td>
              <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-300">{{ kelas.major || '—' }}</td>
              <td class="py-3 px-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
                  {{ kelas.members_count }}
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                  :class="kelas.is_active
                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'">
                  {{ kelas.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button @click="openEdit(kelas)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 dark:border-slate-700 dark:hover:border-amber-500 transition-colors"
                    title="Edit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>
                  </button>
                  <button @click="confirmDelete(kelas)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50 dark:border-slate-700 dark:hover:border-rose-500 transition-colors"
                    title="Hapus">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500">
        <span>Menampilkan <strong class="text-slate-800 dark:text-slate-200">{{ classes.from }}–{{ classes.to }}</strong> dari <strong class="text-slate-800 dark:text-slate-200">{{ classes.total }}</strong> kelas</span>
        <div v-if="classes.last_page > 1" class="flex gap-1">
          <Link v-for="link in classes.links" :key="link.label"
            :href="link.url || '#'"
            class="px-3 py-1 rounded text-xs font-medium transition-colors"
            :class="link.active
              ? 'bg-indigo-600 text-white'
              : link.url
                ? 'border border-slate-200 hover:bg-slate-50 dark:border-slate-700'
                : 'text-slate-300 cursor-not-allowed'"
            v-html="link.label" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="card py-16 text-center">
      <svg width="48" height="48" fill="none" viewBox="0 0 24 24" class="mx-auto mb-4 text-slate-300 dark:text-slate-600">
        <path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"
          stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <div class="text-slate-500 font-medium">Belum ada data kelas</div>
      <p class="text-sm text-slate-400 mt-1">Tambahkan kelas agar siswa bisa memilih saat registrasi</p>
      <button @click="openAdd" class="btn btn-primary mt-6">Tambah Kelas Pertama</button>
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
import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({
  classes: Object,
  filters: Object,
})

// Search
const search = ref(props.filters?.search || '')
function applySearch() {
  router.get(route('kelas.index'), { search: search.value || undefined }, { preserveState: true, replace: true })
}

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
