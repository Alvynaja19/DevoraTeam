<template>
  <AdminLayout :title="isEdit ? 'Edit Buku' : 'Tambah Buku'">
    <div class="card p-6 max-w-4xl mx-auto">
      <h3 class="font-semibold text-lg text-slate-800 dark:text-white mb-6">
        {{ isEdit ? 'Form Edit Data Buku' : 'Form Tambah Buku Baru' }}
      </h3>
      
      <form @submit.prevent="submit" class="space-y-6">
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
          
          <!-- Baris untuk Kota, Tipe Perolehan, Tahun Inventaris -->
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
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({
  book: { type: Object, default: () => null },
  categories: { type: Array, default: () => [] }
})

const isEdit = computed(() => !!props.book)

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
  description: props.book?.description || ''
})

function submit() {
  if (isEdit.value) {
    form.put(route('books.update', props.book.id))
  } else {
    form.post(route('books.store'))
  }
}
</script>
