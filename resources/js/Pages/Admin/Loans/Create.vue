<template>
  <AdminLayout title="Peminjaman Baru">
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Step 1: Scan Anggota -->
      <div class="card">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#2b5a41">1</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan QR Anggota</div>
          </div>
        </div>
        <div class="card-body">
          <div class="flex gap-2">
            <input
              ref="qrInput"
              v-model="memberCode"
              @keyup.enter="validateMember"
              type="text"
              class="form-input flex-1"
              placeholder="Scan QR code atau ketik kode anggota..."
            />
            <button @click="toggleScanner('qr')"
              :class="['btn', showQrScanner ? 'btn-secondary' : 'btn-outline']"
              type="button">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                <path d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {{ showQrScanner ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="validateMember" :disabled="!memberCode || memberLoading" class="btn btn-primary">
              <span v-if="memberLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>Validasi</span>
            </button>
          </div>

          <!-- Camera Scanner QR -->
          <CameraScanner
            type="qr"
            :active="showQrScanner"
            @scanned="onQrScanned"
            @close="showQrScanner = false"
          />

          <!-- Member Info -->
          <div v-if="member" class="mt-4 p-4 rounded-xl flex items-center gap-4" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" style="background:linear-gradient(135deg,#2b5a41,#1c3b2b)">
              {{ member.name[0].toUpperCase() }}
            </div>
            <div class="flex-1">
              <div class="font-semibold text-slate-800">{{ member.name }}</div>
              <div class="text-sm text-slate-500">{{ member.member_code }} · Sisa kuota: <strong class="text-green-600">{{ memberQuota }} buku</strong></div>
            </div>
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div v-if="memberError" class="mt-3 p-3 rounded-xl text-sm text-red-600" style="background:#fef2f2;border:1px solid #fecaca">
            ⚠️ {{ memberError }}
          </div>
        </div>
      </div>

      <!-- Step 2: Scan Buku -->
      <div class="card" :class="!member ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#2b5a41">2</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku (maks. {{ memberQuota }} buku)</div>
          </div>
        </div>
        <div class="card-body space-y-4">
          <div class="flex gap-2">
            <input
              v-model="barcode"
              @keyup.enter="addBook"
              type="text"
              class="form-input flex-1"
              placeholder="Scan barcode atau ketik kode buku..."
            />
            <button @click="toggleScanner('barcode')"
              :class="['btn', showBarcodeScanner ? 'btn-secondary' : 'btn-outline']"
              type="button">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                <path d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"
                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {{ showBarcodeScanner ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="addBook" :disabled="!barcode || bookLoading" class="btn btn-primary">
              <span v-if="bookLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>+ Tambah</span>
            </button>
          </div>

          <!-- Camera Scanner Barcode -->
          <CameraScanner
            type="barcode"
            :active="showBarcodeScanner"
            @scanned="onBarcodeScanned"
            @close="showBarcodeScanner = false"
          />

          <!-- Book List -->
          <div v-if="selectedBooks.length > 0" class="space-y-2">
            <div v-for="(bk, i) in selectedBooks" :key="i" class="flex items-center gap-3 p-3 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#eef2ff">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="#2b5a41" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="flex-1">
                <div class="text-sm font-semibold text-slate-800">{{ bk.book?.title }}</div>
                <div class="text-xs text-slate-400">{{ bk.copy_code }} · {{ bk.book?.author }}</div>
              </div>
              <button @click="removeBook(i)" class="text-red-400 hover:text-red-600">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>
          <div v-if="bookError" class="p-3 rounded-xl text-sm text-red-600" style="background:#fef2f2;border:1px solid #fecaca">
            ⚠️ {{ bookError }}
          </div>
        </div>
      </div>

      <!-- Step 3: Konfirmasi -->
      <div class="card" :class="selectedBooks.length === 0 ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#2b5a41">3</div>
            <div class="font-semibold text-slate-800 dark:text-white">Konfirmasi Peminjaman</div>
          </div>
        </div>
        <div class="card-body space-y-4">
          <div class="form-group">
            <label class="form-label">Tipe Peminjaman</label>
            <select v-model="loanType" class="form-input">
              <option value="pembaca">Pembaca Biasa (~3 hari kerja)</option>
              <option value="lomba">Lomba / Penelitian (~30 hari)</option>
            </select>
          </div>

          <div class="p-4 rounded-xl space-y-2" style="background:#f8fafc;border:1px solid #e2e8f0">
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Anggota</span>
              <span class="font-semibold">{{ member?.name || '-' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Jumlah Buku</span>
              <span class="font-semibold">{{ selectedBooks.length }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Tipe</span>
              <span class="font-semibold capitalize">{{ loanType }}</span>
            </div>
          </div>

          <button @click="submitLoan" :disabled="!member || selectedBooks.length === 0 || submitting" class="btn btn-primary w-full justify-center py-2.5">
            <span v-if="submitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ submitting ? 'Memproses...' : '✓ Konfirmasi Peminjaman' }}
          </button>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'

const memberCode     = ref('')
const barcode        = ref('')
const member         = ref(null)
const memberQuota    = ref(2)
const memberError    = ref('')
const memberLoading  = ref(false)
const bookLoading    = ref(false)
const bookError      = ref('')
const selectedBooks  = ref([])
const loanType       = ref('pembaca')
const submitting     = ref(false)

// Scanner state
const showQrScanner      = ref(false)
const showBarcodeScanner = ref(false)

function toggleScanner(type) {
  if (type === 'qr') {
    showQrScanner.value = !showQrScanner.value
    showBarcodeScanner.value = false
  } else {
    showBarcodeScanner.value = !showBarcodeScanner.value
    showQrScanner.value = false
  }
}

// QR scanned via camera
function onQrScanned(value) {
  memberCode.value = value
  showQrScanner.value = false
  validateMember()
}

// Barcode scanned via camera
function onBarcodeScanned(value) {
  barcode.value = value
  showBarcodeScanner.value = false
  addBook()
}

async function validateMember() {
  if (!memberCode.value) return
  memberLoading.value = true
  memberError.value = ''
  member.value = null

  try {
    const res = await axios.post(route('loans.validate-member'), { member_code: memberCode.value })
    if (res.data.valid) {
      member.value = res.data.member
      memberQuota.value = res.data.quota ?? 2
    } else {
      memberError.value = res.data.message
    }
  } catch (e) {
    memberError.value = e.response?.data?.message || 'Terjadi kesalahan saat validasi anggota.'
  } finally {
    memberLoading.value = false
  }
}

async function addBook() {
  if (!barcode.value || selectedBooks.value.length >= memberQuota.value) return
  bookLoading.value = true
  bookError.value = ''

  try {
    const res = await axios.post(route('loans.validate-book'), { barcode: barcode.value })
    if (res.data.valid) {
      if (selectedBooks.value.find(b => b.barcode === res.data.copy.barcode)) {
        bookError.value = 'Buku ini sudah ditambahkan.'
      } else {
        selectedBooks.value.push(res.data.copy)
      }
    } else {
      bookError.value = res.data.message
    }
    barcode.value = ''
  } catch (e) {
    bookError.value = e.response?.data?.message || 'Buku tidak ditemukan.'
  } finally {
    bookLoading.value = false
  }
}

function removeBook(idx) { selectedBooks.value.splice(idx, 1) }

function submitLoan() {
  submitting.value = true
  router.post(route('loans.store'), {
    member_code:  memberCode.value,
    barcodes:  selectedBooks.value.map(b => b.barcode),
    loan_type: loanType.value,
  }, {
    onFinish: () => { submitting.value = false }
  })
}
</script>
