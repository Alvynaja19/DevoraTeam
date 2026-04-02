<template>
  <AdminLayout :title="isEdit ? 'Edit Buku' : 'Tambah Buku'">
    <div class="card p-6 max-w-4xl mx-auto">
      <h3 class="font-semibold text-lg text-slate-800 dark:text-white mb-6">
        {{ isEdit ? 'Form Edit Data Buku' : 'Form Tambah Buku Baru' }}
      </h3>
      
      <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">

        <!-- ── Section Sampul Buku ── -->
        <div class="form-group space-y-2">
          <label class="form-label font-medium dark:text-slate-300">Sampul Buku</label>
          
          <div class="flex items-start gap-5">
            <!-- Preview Gambar -->
            <div class="shrink-0">
              <div v-if="coverPreview || currentCover" class="relative w-28 h-40 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-600 shadow-sm">
                <img
                  :src="coverPreview || currentCover"
                  alt="Sampul Buku"
                  class="w-full h-full object-cover"
                />
                <!-- Tombol hapus cover -->
                <button
                  type="button"
                  @click="removeCover"
                  class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow"
                  title="Hapus sampul"
                >✕</button>
              </div>
              <!-- Placeholder jika belum ada cover -->
              <div v-else class="w-28 h-40 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-600 flex flex-col items-center justify-center text-slate-400 text-xs gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Belum ada</span>
                <span>sampul</span>
              </div>
            </div>

            <!-- Input & Instruksi -->
            <div class="flex-1 space-y-2">
              <label
                for="cover-upload"
                class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                {{ coverPreview || currentCover ? 'Ganti Sampul' : 'Upload Sampul' }}
              </label>
              <input
                id="cover-upload"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="hidden"
                @change="onCoverChange"
              />
              <p class="text-xs text-slate-400 dark:text-slate-500">
                Format: JPG, PNG, WEBP · Maks. 2 MB<br/>
                Sampul juga bisa diisi otomatis saat import Excel (kolom: <code class="bg-slate-100 dark:bg-slate-700 px-1 rounded">sampul</code>)
              </p>
              <div v-if="form.errors.cover_image" class="text-sm text-red-500">{{ form.errors.cover_image }}</div>
            </div>
          </div>
        </div>

        <!-- ── Field Lainnya ── -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Judul Buku <span class="text-red-500">*</span></label>
            <input type="text" v-model="form.title" class="form-input" required />
            <div v-if="form.errors.title" class="text-sm text-red-500">{{ form.errors.title }}</div>
          </div>
          
          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Penulis <span class="text-red-500">*</span></label>
            <input type="text" v-model="form.author" class="form-input" required />
            <div v-if="form.errors.author" class="text-sm text-red-500">{{ form.errors.author }}</div>
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Kategori <span class="text-red-500">*</span></label>
            <select v-model="form.category_id" class="form-input" required>
              <option value="">Pilih Kategori...</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <div v-if="form.errors.category_id" class="text-sm text-red-500">{{ form.errors.category_id }}</div>
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">ISBN</label>
            <input type="text" v-model="form.isbn" class="form-input" />
            <div v-if="form.errors.isbn" class="text-sm text-red-500">{{ form.errors.isbn }}</div>
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Penerbit</label>
            <input type="text" v-model="form.publisher" class="form-input" />
            <div v-if="form.errors.publisher" class="text-sm text-red-500">{{ form.errors.publisher }}</div>
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">No Klasifikasi</label>
            <input type="text" v-model="form.classification_number" class="form-input" />
            <div v-if="form.errors.classification_number" class="text-sm text-red-500">{{ form.errors.classification_number }}</div>
          </div>
          
          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Kota Penerbit</label>
            <input type="text" v-model="form.city" class="form-input" />
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Tahun Terbit</label>
            <input type="text" v-model="form.year" class="form-input" />
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Tipe Perolehan</label>
            <input type="text" v-model="form.acquisition_type" class="form-input" placeholder="Misal: Beli, Sumbangan" />
          </div>

          <div class="form-group space-y-1">
            <label class="form-label font-medium dark:text-slate-300">Tahun Inventaris</label>
            <input type="text" v-model="form.inventory_year" class="form-input" />
          </div>
        </div>

        <div class="form-group space-y-1">
          <label class="form-label font-medium dark:text-slate-300">Deskripsi / Sinopsis Lengkap</label>
          <textarea v-model="form.description" rows="4" class="form-input" placeholder="Tambahkan informasi rinci tentang buku ini..."></textarea>
          <div v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
          <Link :href="route('books.index')" class="btn btn-secondary px-6">Batal</Link>
          <button type="submit" class="btn btn-primary px-8" :disabled="form.processing">
            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Buku' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({
  book: { type: Object, default: () => null },
  categories: { type: Array, default: () => [] }
})

const isEdit = computed(() => !!props.book)

// Cover image state
const coverPreview = ref(null)
const removeCoverFlag = ref(false)

// URL sampul saat ini (dari DB) — bisa URL eksternal atau path storage
const currentCover = computed(() => {
  if (removeCoverFlag.value) return null
  if (!props.book?.cover_image) return null
  const c = props.book.cover_image
  return c.startsWith('http') ? c : `/storage/${c}`
})

function onCoverChange(e) {
  const file = e.target.files[0]
  if (!file) return
  removeCoverFlag.value = false
  form.cover_image = file
  coverPreview.value = URL.createObjectURL(file)
}

function removeCover() {
  form.cover_image = null
  coverPreview.value = null
  removeCoverFlag.value = true
  form.remove_cover = true
  // Reset input file
  const input = document.getElementById('cover-upload')
  if (input) input.value = ''
}

const form = useForm({
  title: props.book?.title || '',
  author: props.book?.author || '',
  category_id: props.book?.category_id || '',
  isbn: props.book?.isbn || '',
  publisher: props.book?.publisher || '',
  classification_number: props.book?.classification_number || '',
  city: props.book?.city || '',
  year: props.book?.year || '',
  acquisition_type: props.book?.acquisition_type || '',
  inventory_year: props.book?.inventory_year || '',
  description: props.book?.description || '',
  cover_image: null,
  remove_cover: false,
})

function submit() {
  if (isEdit.value) {
    form.post(route('books.update', props.book.id), {
      _method: 'put',
    })
  } else {
    form.post(route('books.store'))
  }
}
</script>
