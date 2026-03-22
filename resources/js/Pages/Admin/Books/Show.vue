<template>
  <AdminLayout :title="book.title">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Kolom Info Buku -->
      <div class="md:col-span-1 space-y-6">
        <div class="card overflow-hidden">
          <div class="h-48 flex items-center justify-center relative" :style="`background:linear-gradient(135deg, ${coverColor(book.category_id)})`">
            <svg width="60" height="60" fill="none" viewBox="0 0 24 24" class="opacity-40"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="absolute top-3 right-3">
              <span class="badge" :class="book.total_copies > 0 ? 'badge-green' : 'badge-red'">
                {{ book.total_copies }} eks
              </span>
            </div>
          </div>
          <div class="p-5">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white leading-tight mb-2">{{ book.title }}</h3>
            <div class="text-slate-600 dark:text-slate-300 text-sm space-y-2 mt-4">
              <div class="flex justify-between">
                <span class="text-slate-400">Kategori:</span>
                <span class="font-medium">{{ book.category?.name || '-' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Penulis:</span>
                <span class="font-medium text-right">{{ book.author }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Penerbit:</span>
                <span class="font-medium text-right">{{ book.publisher || '-' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">Tahun:</span>
                <span class="font-medium">{{ book.year || '-' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-400">ISBN:</span>
                <span class="font-medium">{{ book.isbn || '-' }}</span>
              </div>
              <div class="flex justify-between" v-if="book.classification_number">
                <span class="text-slate-400">No Klasifikasi:</span>
                <span class="font-medium">{{ book.classification_number }}</span>
              </div>
            </div>
            
            <div class="mt-6 flex gap-2">
              <Link :href="route('books.edit', book.id)" class="btn btn-secondary w-full justify-center">Edit Buku</Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Eksemplar -->
      <div class="md:col-span-2 space-y-6">
        <div class="card p-6">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-semibold text-slate-800 dark:text-white">Eksemplar / Copy Buku</h4>
            <button @click="isAddCopyModalOpen = true" class="btn btn-primary text-sm py-1.5">
              + Tambah Detail Fisik
            </button>
          </div>

          <div class="overflow-x-auto rounded-lg border border-slate-100 dark:border-slate-700" v-if="book.copies && book.copies.length > 0">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm dark:bg-slate-800/50 dark:border-slate-700">
                  <th class="py-3 px-4 font-medium"># Kode Copy</th>
                  <th class="py-3 px-4 font-medium">Barcode Fisik</th>
                  <th class="py-3 px-4 font-medium">Kondisi</th>
                  <th class="py-3 px-4 font-medium text-right">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                <tr v-for="copy in book.copies" :key="copy.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                  <td class="py-3 px-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-medium whitespace-nowrap">
                    {{ copy.copy_code }}
                  </td>
                  <td class="py-3 px-4">
                    <div class="bg-white p-1.5 rounded inline-block shadow-sm">
                      <svg class="barcode-svg" :data-barcode="copy.barcode" style="max-height: 48px; width: auto;"></svg>
                    </div>
                  </td>
                  <td class="py-3 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium" 
                          :class="{'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400': copy.condition === 'baik', 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400': copy.condition !== 'baik'}">
                      {{ copy.condition }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                          :class="{'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400': copy.status === 'tersedia', 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400': copy.status === 'dipinjam'}">
                      {{ copy.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-8 text-slate-400 text-sm">
            Belum ada data eksemplar fisik buku ini.
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Eksemplar -->
    <div v-if="isAddCopyModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden dark:bg-slate-800">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
          <h3 class="font-semibold text-lg text-slate-800 dark:text-white">Tambah Eksemplar Fisik</h3>
        </div>
        
        <form @submit.prevent="submitCopy" class="p-6">
          <div class="space-y-4">
            <div class="form-group">
              <label class="form-label dark:text-slate-300">Kode Copy</label>
              <input type="text" v-model="copyForm.copy_code" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label dark:text-slate-300">Barcode</label>
              <input type="text" v-model="copyForm.barcode" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label dark:text-slate-300">Kondisi</label>
              <select v-model="copyForm.condition" class="form-input" required>
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
                <option value="hilang">Hilang</option>
              </select>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="isAddCopyModalOpen = false" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="copyForm.processing">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import JsBarcode from 'jsbarcode'

const props = defineProps({ book: Object })

function drawBarcodes() {
  document.querySelectorAll('.barcode-svg').forEach(el => {
    JsBarcode(el, el.dataset.barcode, {
      format: 'CODE128',
      height: 30,
      displayValue: true,
      fontSize: 12,
      margin: 0
    })
  })
}

onMounted(() => {
  drawBarcodes()
})

watch(() => props.book.copies, async () => {
  await nextTick()
  drawBarcodes()
}, { deep: true })

const colors = ['#6366f1','#8b5cf6','#0ea5e9','#6366f1','#10b981','#059669','#f59e0b','#ef4444','#ec4899','#8b5cf6']
function coverColor(id) { return colors[((id || 0) - 1) % colors.length] }

const isAddCopyModalOpen = ref(false)

// Generate draft code
const draftNum = (props.book.copies?.length || 0) + 1;
const bookIdStr = String(props.book.id).padStart(4, '0');

const copyForm = useForm({
  copy_code: `BK-${bookIdStr}-C${draftNum}`,
  barcode: `BC${String(Date.now()).slice(-5)}${Math.floor(Math.random()*90)+10}${props.book.id}${draftNum}`,
  condition: 'baik'
})

function submitCopy() {
  copyForm.post(route('books.copies.store', props.book.id), {
    onSuccess: () => {
      isAddCopyModalOpen.value = false
      copyForm.reset()
      // re-draft for next
      const nextNum = draftNum + 1;
      copyForm.copy_code = `BK-${bookIdStr}-C${nextNum}`
      copyForm.barcode = `BC${String(Date.now()).slice(-5)}${Math.floor(Math.random()*90)+10}${props.book.id}${nextNum}`
    }
  })
}
</script>
