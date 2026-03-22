<template>
  <AdminLayout title="Pengembalian Buku">
    <div class="max-w-2xl mx-auto space-y-6">
      <!-- Scanner -->
      <div class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800">Scan Barcode Buku</div>
        </div>
        <div class="card-body">
          <div class="flex gap-3">
            <input
              ref="barcodeInput"
              v-model="barcode"
              @keyup.enter="checkBook"
              type="text"
              class="form-input flex-1 text-lg"
              placeholder="Scan barcode buku yang dikembalikan..."
              autofocus
            />
            <button @click="checkBook" :disabled="!barcode || loading" class="btn btn-primary px-6">
              <span v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>Cek</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Result -->
      <div v-if="result" class="card fade-in">
        <div class="card-header">
          <div class="font-semibold text-slate-800">Informasi Buku</div>
        </div>
        <div class="card-body space-y-4">
          <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
            <div>
              <div class="text-xs text-slate-400 mb-1">Judul Buku</div>
              <div class="font-semibold text-slate-800">{{ result.copy?.book?.title }}</div>
            </div>
            <div>
              <div class="text-xs text-slate-400 mb-1">Kode Eksemplar</div>
              <div class="font-mono text-sm">{{ result.copy?.copy_code }}</div>
            </div>
            <div>
              <div class="text-xs text-slate-400 mb-1">Anggota</div>
              <div class="font-semibold text-slate-800">{{ result.member?.name }}</div>
            </div>
            <div>
              <div class="text-xs text-slate-400 mb-1">Jatuh Tempo</div>
              <div class="font-semibold" :class="result.is_overdue ? 'text-red-600' : 'text-green-600'">
                {{ formatDate(result.due_date) }}
                <span v-if="result.is_overdue" class="badge badge-red ml-2">Terlambat!</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Kondisi Buku Setelah Dikembalikan</label>
            <div class="grid grid-cols-2 gap-3">
              <label v-for="opt in conditions" :key="opt.value" class="cursor-pointer">
                <input v-model="condition" type="radio" :value="opt.value" class="sr-only"/>
                <div class="p-3 rounded-xl border-2 text-center text-sm font-medium transition-all" :class="condition === opt.value ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                  {{ opt.icon }} {{ opt.label }}
                </div>
              </label>
            </div>
          </div>

          <button @click="processReturn" :disabled="!condition || processing" class="btn btn-primary w-full justify-center py-3">
            <span v-if="processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ processing ? 'Memproses...' : '✓ Proses Pengembalian' }}
          </button>
        </div>
      </div>

      <!-- Fine Result -->
      <div v-if="fineResult" class="card fade-in">
        <div class="card-body">
          <div v-if="fineResult.fines?.length > 0" class="p-4 rounded-xl" style="background:#fef2f2;border:1px solid #fecaca">
            <div class="font-semibold text-red-700 mb-2">⚠️ Denda Terdeteksi</div>
            <div v-for="fine in fineResult.fines" :key="fine.id" class="flex justify-between text-sm text-red-600 py-1">
              <span>{{ fineLabel(fine.fine_type) }}</span>
              <span class="font-bold">{{ formatRupiah(fine.amount) }}</span>
            </div>
          </div>
          <div v-else class="p-4 rounded-xl text-center" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div class="text-green-600 font-semibold">✓ Pengembalian berhasil, tidak ada denda!</div>
          </div>
          <button @click="resetForm" class="btn btn-secondary w-full justify-center mt-4">Scan Buku Berikutnya</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const barcode   = ref('')
const result    = ref(null)
const fineResult= ref(null)
const condition = ref('')
const loading   = ref(false)
const processing= ref(false)

const conditions = [
  { value: 'baik',        icon: '✅', label: 'Baik' },
  { value: 'rusak_ringan',icon: '⚠️', label: 'Rusak Ringan' },
  { value: 'rusak_berat', icon: '🔴', label: 'Rusak Berat' },
  { value: 'hilang',      icon: '❌', label: 'Hilang' },
]

async function checkBook() {
  if (!barcode.value) return
  loading.value = true
  result.value = null
  fineResult.value = null
  condition.value = ''

  try {
    const res = await axios.post(route('returns.check'), { barcode: barcode.value })
    if (res.data.on_loan) {
      result.value = res.data
    } else {
      alert(res.data.message)
    }
  } catch (e) {
    alert(e.response?.data?.message || 'Buku tidak ditemukan.')
  } finally {
    loading.value = false
  }
}

async function processReturn() {
  processing.value = true
  try {
    const res = await axios.post(route('returns.store'), {
      barcode: barcode.value, condition_after: condition.value
    })
    fineResult.value = res.data
    result.value = null
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal memproses pengembalian.')
  } finally {
    processing.value = false
  }
}

function resetForm() { barcode.value = ''; result.value = null; fineResult.value = null; condition.value = '' }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) }
function formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') }
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }
</script>
