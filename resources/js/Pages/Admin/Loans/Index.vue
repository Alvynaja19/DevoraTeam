<template>
  <AdminLayout title="Peminjaman Buku">
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Step 1: Scan Anggota -->
      <div class="card">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">1</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan QR Anggota</div>
          </div>
        </div>
        <div class="card-body">
          <div class="flex gap-2">
            <input v-model="pinjam.memberCode" @keyup.enter="pinjamValidateMember" type="text"
              class="form-input flex-1" placeholder="Scan QR code atau ketik kode anggota..." />
            <button @click="toggleScanner('pinjam-qr')" type="button"
              :class="['btn', showScanner === 'pinjam-qr' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'pinjam-qr' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="pinjamValidateMember" :disabled="!pinjam.memberCode || pinjam.memberLoading" class="btn btn-primary">
              <span v-if="pinjam.memberLoading" class="spinner"></span>
              <span v-else>Validasi</span>
            </button>
          </div>

          <CameraScanner type="qr" :active="showScanner === 'pinjam-qr'"
            @scanned="v => { pinjam.memberCode = v; showScanner = ''; pinjamValidateMember() }"
            @close="showScanner = ''" />

          <!-- Member Info -->
          <div v-if="pinjam.member" class="mt-4 p-4 rounded-xl flex items-center gap-4 bg-emerald-50 border border-emerald-200">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 bg-gradient-to-br from-[#2b5a41] to-[#1c3b2b]">
              {{ pinjam.member.name[0].toUpperCase() }}
            </div>
            <div class="flex-1">
              <div class="font-semibold text-slate-800">{{ pinjam.member.name }}</div>
              <div class="text-sm text-slate-500">{{ pinjam.member.member_code }} · Sisa kuota: <strong class="text-green-600">{{ pinjam.quota }} buku</strong></div>
            </div>
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div v-if="pinjam.memberError" class="mt-3 p-3 rounded-xl text-sm text-red-600 bg-red-50 border border-red-200">⚠️ {{ pinjam.memberError }}</div>
        </div>
      </div>

      <!-- Step 2: Scan Buku -->
      <div class="card" :class="!pinjam.member ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">2</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku (maks. {{ pinjam.quota }} buku)</div>
          </div>
        </div>
        <div class="card-body space-y-4">

          <!-- Big Camera Scan Button (Primary) -->
          <div v-if="showScanner !== 'pinjam-barcode'" class="scan-btn-area" @click="openBookScanner">
            <div class="scan-icon">
              <svg width="36" height="36" fill="none" viewBox="0 0 24 24">
                <path d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75V16.5ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75H13.5V13.5ZM13.5 18.75h.75v.75H13.5v-.75ZM18.75 13.5h.75v.75h-.75V13.5ZM18.75 18.75h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75V16.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="scan-label">Tap untuk Scan Barcode Buku</div>
            <div class="scan-sublabel">Arahkan kamera ke barcode buku</div>
          </div>

          <!-- Camera Scanner (terbuka saat aktif) -->
          <CameraScanner type="barcode" :active="showScanner === 'pinjam-barcode'"
            @scanned="v => { pinjam.barcode = v; showScanner = ''; pinjamAddBook() }"
            @close="showScanner = ''; focusBookInput()" />

          <!-- Manual Input (alternatif) -->
          <div class="manual-input-row">
            <span class="manual-label">atau ketik manual:</span>
            <input ref="bookBarcodeInput" v-model="pinjam.barcode" @keyup.enter="pinjamAddBook" type="text"
              class="form-input flex-1" placeholder="Ketik kode barcode..." />
            <button @click="pinjamAddBook" :disabled="!pinjam.barcode || pinjam.bookLoading" class="btn btn-primary">
              <span v-if="pinjam.bookLoading" class="spinner"></span>
              <span v-else>+ Tambah</span>
            </button>
          </div>

          <!-- Daftar buku yang sudah di-scan -->
          <div v-if="pinjam.books.length > 0" class="space-y-2">
            <div v-for="(bk, i) in pinjam.books" :key="i" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-indigo-50">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="#2b5a41" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="flex-1">
                <div class="text-sm font-semibold text-slate-800">{{ bk.book?.title }}</div>
                <div class="text-xs text-slate-400">{{ bk.copy_code }} · {{ bk.book?.author }}</div>
              </div>
              <button @click="pinjam.books.splice(i, 1)" class="text-red-400 hover:text-red-600">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>

          <!-- Tombol scan buku berikutnya (jika sudah ada buku) -->
          <button v-if="pinjam.books.length > 0 && pinjam.books.length < pinjam.quota"
            @click="openBookScanner"
            class="btn btn-outline w-full justify-center gap-2">
            📷 Scan Buku Berikutnya
          </button>

          <div v-if="pinjam.bookError" class="p-3 rounded-xl text-sm text-red-600 bg-red-50 border border-red-200">⚠️ {{ pinjam.bookError }}</div>
        </div>
      </div>

      <!-- Step 3: Konfirmasi -->
      <div class="card" :class="pinjam.books.length === 0 ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">3</div>
            <div class="font-semibold text-slate-800 dark:text-white">Konfirmasi Peminjaman</div>
          </div>
        </div>
        <div class="card-body space-y-4">
          <div class="form-group">
            <label class="form-label">Tipe Peminjaman</label>
            <select v-model="pinjam.loanType" class="form-input">
              <option value="pembaca">Pembaca Biasa (~3 hari kerja)</option>
              <option value="lomba">Lomba / Penelitian (~30 hari)</option>
            </select>
          </div>
          <div class="p-4 rounded-xl space-y-2 bg-slate-50 border border-slate-200">
            <div class="flex justify-between text-sm"><span class="text-slate-500">Anggota</span><span class="font-semibold">{{ pinjam.member?.name || '-' }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">Jumlah Buku</span><span class="font-semibold">{{ pinjam.books.length }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">Tipe</span><span class="font-semibold capitalize">{{ pinjam.loanType }}</span></div>
          </div>
          <button @click="pinjamSubmit" :disabled="!pinjam.member || pinjam.books.length === 0 || pinjam.submitting" class="btn btn-primary w-full justify-center py-2.5">
            <span v-if="pinjam.submitting" class="spinner"></span>
            {{ pinjam.submitting ? 'Memproses...' : '✓ Konfirmasi Peminjaman' }}
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'

const showScanner = ref('')
const bookBarcodeInput = ref(null)

function toggleScanner(id) {
  showScanner.value = showScanner.value === id ? '' : id
}

function openBookScanner() {
  showScanner.value = 'pinjam-barcode'
}

function focusBookInput() {
  nextTick(() => {
    if (bookBarcodeInput.value) bookBarcodeInput.value.focus()
  })
}

const pinjam = reactive({
  memberCode: '', barcode: '', member: null, quota: 2,
  memberError: '', memberLoading: false,
  bookLoading: false, bookError: '', books: [],
  loanType: 'pembaca', submitting: false,
})

async function pinjamValidateMember() {
  if (!pinjam.memberCode) return
  pinjam.memberLoading = true; pinjam.memberError = ''; pinjam.member = null
  try {
    const res = await axios.post(route('loans.validate-member'), { member_code: pinjam.memberCode })
    if (res.data.valid) {
      pinjam.member = res.data.member
      pinjam.quota = res.data.quota ?? 2
      // Otomatis buka kamera untuk scan buku setelah anggota tervalidasi
      nextTick(() => { showScanner.value = 'pinjam-barcode' })
    }
    else { pinjam.memberError = res.data.message }
  } catch (e) { pinjam.memberError = e.response?.data?.message || 'Terjadi kesalahan.' }
  finally { pinjam.memberLoading = false }
}

async function pinjamAddBook() {
  if (!pinjam.barcode || pinjam.books.length >= pinjam.quota) return
  pinjam.bookLoading = true; pinjam.bookError = ''
  try {
    const res = await axios.post(route('loans.validate-book'), { barcode: pinjam.barcode })
    if (res.data.valid) {
      if (pinjam.books.find(b => b.barcode === res.data.copy.barcode)) { pinjam.bookError = 'Buku ini sudah ditambahkan.' }
      else { pinjam.books.push(res.data.copy) }
    } else { pinjam.bookError = res.data.message }
    pinjam.barcode = ''
    focusBookInput()
  } catch (e) { pinjam.bookError = e.response?.data?.message || 'Buku tidak ditemukan.' }
  finally { pinjam.bookLoading = false }
}

function pinjamSubmit() {
  pinjam.submitting = true
  router.post(route('loans.store'), {
    member_code: pinjam.memberCode, barcodes: pinjam.books.map(b => b.barcode), loan_type: pinjam.loanType,
  }, { onFinish: () => { pinjam.submitting = false } })
}
</script>

<style scoped>
.step-badge {
  width: 28px; height: 28px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: white; font-size: 12px; font-weight: 700;
  background: #2b5a41;
}
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Big scan button */
.scan-btn-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 28px 20px;
  border: 2px dashed #6366f1;
  border-radius: 16px;
  background: linear-gradient(135deg, #f0f1ff 0%, #fafafa 100%);
  cursor: pointer;
  transition: all 0.2s ease;
  user-select: none;
}
.scan-btn-area:hover {
  border-color: #4f46e5;
  background: linear-gradient(135deg, #e0e3ff 0%, #f5f5ff 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.15);
}
.scan-icon {
  width: 72px; height: 72px;
  border-radius: 20px;
  background: #6366f1;
  color: white;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
}
.scan-label {
  font-size: 15px;
  font-weight: 700;
  color: #4f46e5;
}
.scan-sublabel {
  font-size: 12px;
  color: #94a3b8;
}

/* Manual input row */
.manual-input-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.manual-label {
  font-size: 12px;
  color: #94a3b8;
  white-space: nowrap;
}
</style>
