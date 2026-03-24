<template>
  <AdminLayout title="Pengembalian Buku">
    <div class="max-w-2xl mx-auto space-y-6">

      <!-- Scanner -->
      <div class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku</div>
        </div>
        <div class="card-body">
          <form @submit.prevent="retur.result ? returProcess() : returCheckBook()" class="flex gap-2">
            <input ref="barcodeInput" v-model="retur.barcode" type="text"
              class="form-input flex-1 text-lg" placeholder="Scan barcode buku yang dikembalikan..." autofocus />
            <button @click="toggleScanner('retur-barcode')" type="button" tabindex="-1"
              :class="['btn', showScanner === 'retur-barcode' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'retur-barcode' ? 'Tutup' : 'Kamera' }}
            </button>
            <button type="submit" :disabled="!retur.barcode || retur.loading" class="btn btn-primary px-6">
              <span v-if="retur.loading" class="spinner"></span>
              <span v-else>Cek</span>
            </button>
          </form>

          <CameraScanner type="barcode" :active="showScanner === 'retur-barcode'"
            @scanned="v => { retur.barcode = v; showScanner = ''; barcodeInput?.focus(); returCheckBook() }"
            @close="showScanner = ''; barcodeInput?.focus()" />
        </div>
      </div>

      <!-- Result -->
      <div v-if="retur.result" class="card">
        <div class="card-header"><div class="font-semibold text-slate-800 dark:text-white">Informasi Buku</div></div>
        <div class="card-body space-y-4">
          <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div><div class="text-xs text-slate-400 mb-1">Judul Buku</div><div class="font-semibold text-slate-800">{{ retur.result.copy?.book?.title }}</div></div>
            <div><div class="text-xs text-slate-400 mb-1">Kode Eksemplar</div><div class="font-mono text-sm">{{ retur.result.copy?.copy_code }}</div></div>
            <div><div class="text-xs text-slate-400 mb-1">Anggota</div><div class="font-semibold text-slate-800">{{ retur.result.member?.name }}</div></div>
            <div>
              <div class="text-xs text-slate-400 mb-1">Jatuh Tempo</div>
              <div class="font-semibold" :class="retur.result.is_overdue ? 'text-red-600' : 'text-green-600'">
                {{ formatDate(retur.result.due_date) }}
                <span v-if="retur.result.is_overdue" class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600">Terlambat!</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Kondisi Buku Setelah Dikembalikan</label>
            <div class="grid grid-cols-2 gap-3">
              <label v-for="opt in returConditions" :key="opt.value" class="cursor-pointer">
                <input v-model="retur.condition" type="radio" :value="opt.value" class="sr-only" />
                <div class="p-3 rounded-xl border-2 text-center text-sm font-medium transition-all"
                  :class="retur.condition === opt.value ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                  {{ opt.icon }} {{ opt.label }}
                </div>
              </label>
            </div>
          </div>

          <button @click="returProcess" :disabled="!retur.condition || retur.processing" class="btn btn-primary w-full justify-center py-3">
            <span v-if="retur.processing" class="spinner"></span>
            {{ retur.processing ? 'Memproses...' : '✓ Proses Pengembalian' }}
          </button>
        </div>
      </div>

      <!-- Fine Result -->
      <div v-if="retur.fineResult" class="card">
        <div class="card-body">
          <div v-if="retur.fineResult.fines?.length > 0" class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="font-semibold text-red-700 mb-2">⚠️ Denda Terdeteksi</div>
            <div v-for="fine in retur.fineResult.fines" :key="fine.id" class="flex justify-between text-sm text-red-600 py-1">
              <span>{{ fineLabel(fine.fine_type) }}</span>
              <span class="font-bold">{{ formatRupiah(fine.amount) }}</span>
            </div>
          </div>
          <div v-else class="p-4 rounded-xl text-center bg-emerald-50 border border-emerald-200">
            <div class="text-green-600 font-semibold">✓ Pengembalian berhasil, tidak ada denda!</div>
          </div>
          <button @click="returReset" class="btn btn-secondary w-full justify-center mt-4">Scan Buku Berikutnya</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'
import { useNotificationStore } from '@/stores/notification'

const notify = useNotificationStore()

const barcodeInput = ref(null)
const showScanner = ref('')
function toggleScanner(id) {
  showScanner.value = showScanner.value === id ? '' : id
}

const retur = reactive({
  barcode: '', result: null, fineResult: null,
  condition: '', loading: false, processing: false,
})

const returConditions = [
  { value: 'baik',         icon: '✅', label: 'Baik' },
  { value: 'rusak_ringan', icon: '⚠️', label: 'Rusak Ringan' },
  { value: 'rusak_berat',  icon: '🔴', label: 'Rusak Berat' },
  { value: 'hilang',       icon: '❌', label: 'Hilang' },
]

async function returCheckBook() {
  if (!retur.barcode) return
  retur.loading = true; retur.result = null; retur.fineResult = null; retur.condition = ''
  try {
    const res = await axios.post(route('returns.check'), { barcode: retur.barcode })
    if (res.data.on_loan) {
      retur.result = res.data
      retur.condition = 'baik' // Auto-select condition 'baik' by default
    }
    else { notify.error(res.data.message || 'Buku tidak sedang dipinjam') }
  } catch (e) { notify.error(e.response?.data?.message || 'Buku tidak ditemukan.') }
  finally { retur.loading = false }
}

async function returProcess() {
  retur.processing = true
  try {
    const res = await axios.post(route('returns.store'), { barcode: retur.barcode, condition_after: retur.condition })
    retur.fineResult = res.data; retur.result = null
    notify.success('Pengembalian berhasil diproses!')
    
    // Auto refresh global data on standard inertia layout
    router.reload()

    // Auto reset scanner form jika TIDAK ADA denda setelah 1.5 detik
    if (!res.data.fines || res.data.fines.length === 0) {
      setTimeout(() => {
        returReset()
      }, 1500)
    }
  } catch (e) { notify.error(e.response?.data?.message || 'Gagal memproses pengembalian.') }
  finally { retur.processing = false }
}

function returReset() { 
  retur.barcode = ''; retur.result = null; retur.fineResult = null; retur.condition = '' 
  if (barcodeInput.value) barcodeInput.value.focus()
}

// ── Helpers ──
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }
function formatDate(d) { return d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }
function formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') }
</script>

<style scoped>
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
