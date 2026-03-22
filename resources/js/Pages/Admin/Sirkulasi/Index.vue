<template>
  <AdminLayout :title="pageTitle">

    <!-- ════════════════════════════════════════════════
         PEMINJAMAN
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'peminjaman'" class="max-w-3xl mx-auto space-y-6">

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
            <input v-model="pinjam.qrToken" @keyup.enter="pinjamValidateMember" type="text"
              class="form-input flex-1" placeholder="Scan QR code anggota atau ketik QR token..." />
            <button @click="toggleScanner('pinjam-qr')" type="button"
              :class="['btn', showScanner === 'pinjam-qr' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'pinjam-qr' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="pinjamValidateMember" :disabled="!pinjam.qrToken || pinjam.memberLoading" class="btn btn-primary">
              <span v-if="pinjam.memberLoading" class="spinner"></span>
              <span v-else>Validasi</span>
            </button>
          </div>

          <CameraScanner type="qr" :active="showScanner === 'pinjam-qr'"
            @scanned="v => { pinjam.qrToken = v; showScanner = ''; pinjamValidateMember() }"
            @close="showScanner = ''" />

          <!-- Member Info -->
          <div v-if="pinjam.member" class="mt-4 p-4 rounded-xl flex items-center gap-4 bg-emerald-50 border border-emerald-200">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600">
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
          <div class="flex gap-2">
            <input v-model="pinjam.barcode" @keyup.enter="pinjamAddBook" type="text"
              class="form-input flex-1" placeholder="Scan barcode atau ketik kode buku..." />
            <button @click="toggleScanner('pinjam-barcode')" type="button"
              :class="['btn', showScanner === 'pinjam-barcode' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'pinjam-barcode' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="pinjamAddBook" :disabled="!pinjam.barcode || pinjam.bookLoading" class="btn btn-primary">
              <span v-if="pinjam.bookLoading" class="spinner"></span>
              <span v-else>+ Tambah</span>
            </button>
          </div>

          <CameraScanner type="barcode" :active="showScanner === 'pinjam-barcode'"
            @scanned="v => { pinjam.barcode = v; showScanner = ''; pinjamAddBook() }"
            @close="showScanner = ''" />

          <div v-if="pinjam.books.length > 0" class="space-y-2">
            <div v-for="(bk, i) in pinjam.books" :key="i" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-indigo-50">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="#6366f1" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
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

    <!-- ════════════════════════════════════════════════
         TAB 2: PENGEMBALIAN
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'pengembalian'" class="max-w-2xl mx-auto space-y-6">

      <!-- Scanner -->
      <div class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku</div>
        </div>
        <div class="card-body">
          <div class="flex gap-2">
            <input v-model="retur.barcode" @keyup.enter="returCheckBook" type="text"
              class="form-input flex-1 text-lg" placeholder="Scan barcode buku yang dikembalikan..." autofocus />
            <button @click="toggleScanner('retur-barcode')" type="button"
              :class="['btn', showScanner === 'retur-barcode' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'retur-barcode' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="returCheckBook" :disabled="!retur.barcode || retur.loading" class="btn btn-primary px-6">
              <span v-if="retur.loading" class="spinner"></span>
              <span v-else>Cek</span>
            </button>
          </div>

          <CameraScanner type="barcode" :active="showScanner === 'retur-barcode'"
            @scanned="v => { retur.barcode = v; showScanner = ''; returCheckBook() }"
            @close="showScanner = ''" />
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

    <!-- ════════════════════════════════════════════════
         TAB 3: RIWAYAT
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'riwayat'">

      <!-- Filters -->
      <div class="card px-4 py-3 mb-4">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-widest shrink-0">Filter</span>
          <select v-model="riwayat.status" @change="riwayatApply" class="form-input py-1.5 px-3 text-sm w-auto min-w-[150px]">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="diperpanjang">Diperpanjang</option>
            <option value="terlambat">Terlambat</option>
            <option value="selesai">Selesai</option>
          </select>
          <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-600 dark:text-slate-400">
            <input v-model="riwayat.overdue" type="checkbox" @change="riwayatApply" class="w-4 h-4 rounded" />
            Hanya Terlambat
          </label>
          <div class="h-5 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
          <div class="relative flex-1 min-w-0">
            <input v-model="riwayat.search" @keyup.enter="riwayatApply" type="text"
              placeholder="🔍 Cari kode / nama anggota..." class="form-input px-3 py-1.5 text-sm w-full" />
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card overflow-hidden" v-if="loans.data.length > 0">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-700">
                <th class="py-3 px-4 font-medium w-12 text-center">No</th>
                <th class="py-3 px-4 font-medium">Kode Pinjam</th>
                <th class="py-3 px-4 font-medium">Anggota</th>
                <th class="py-3 px-4 font-medium">Buku</th>
                <th class="py-3 px-4 font-medium">Tgl Pinjam</th>
                <th class="py-3 px-4 font-medium">Jatuh Tempo</th>
                <th class="py-3 px-4 font-medium text-center">Status</th>
                <th class="py-3 px-4 font-medium text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
              <tr v-for="(loan, i) in loans.data" :key="loan.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-3 px-4 text-center text-sm text-slate-500">{{ loans.from + i }}</td>
                <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ loan.loan_code }}</td>
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600">
                      {{ loan.member?.name?.[0]?.toUpperCase() }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ loan.member?.name }}</div>
                      <div class="text-xs text-slate-400">{{ loan.member?.member_code }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="text-sm text-slate-600 dark:text-slate-300">{{ loan.items?.length }} buku</div>
                  <div class="text-xs text-slate-400 truncate max-w-xs">{{ loan.items?.map(i => i.copy?.book?.title).join(', ') }}</div>
                </td>
                <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-300">{{ formatDate(loan.loan_date) }}</td>
                <td class="py-3 px-4">
                  <span class="text-sm font-medium" :class="isOverdue(loan) ? 'text-red-600' : 'text-slate-600 dark:text-slate-300'">
                    {{ formatDate(loan.extended_due_date || loan.due_date) }}
                  </span>
                  <span v-if="isOverdue(loan)" class="block text-xs text-red-400">terlambat!</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wide"
                    :class="{
                      'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400': loan.status === 'aktif',
                      'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400': loan.status === 'diperpanjang',
                      'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400': loan.status === 'terlambat',
                      'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400': loan.status === 'selesai',
                    }">
                    {{ loan.status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <Link :href="route('loans.show', loan.id)"
                      class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 dark:border-slate-700 transition-colors"
                      title="Detail">
                      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    </Link>
                    <button v-if="['aktif','diperpanjang'].includes(loan.status)"
                      @click="riwayatExtend(loan)"
                      class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 dark:border-slate-700 transition-colors"
                      title="Perpanjang">
                      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500">
          <span>Menampilkan <strong class="text-slate-800 dark:text-slate-200">{{ loans.from }}–{{ loans.to }}</strong> dari <strong class="text-slate-800 dark:text-slate-200">{{ loans.total }}</strong> transaksi</span>
          <div v-if="loans.last_page > 1" class="flex gap-1">
            <Link v-for="link in loans.links" :key="link.label" :href="link.url || '#'"
              class="px-3 py-1 rounded text-xs font-medium transition-colors"
              :class="link.active ? 'bg-indigo-600 text-white' : link.url ? 'border border-slate-200 hover:bg-slate-50 dark:border-slate-700' : 'text-slate-300 cursor-not-allowed'"
              v-html="link.label" />
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="card py-16 text-center">
        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" class="mx-auto mb-4 text-slate-300"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div class="text-slate-500 font-medium">Belum ada riwayat transaksi</div>
        <p class="text-sm text-slate-400 mt-1">Buat peminjaman pertama di menu Peminjaman</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'

const props = defineProps({ page: String, loans: Object, filters: Object })

const pageTitle = computed(() => ({
  peminjaman: 'Peminjaman',
  pengembalian: 'Pengembalian',
  riwayat: 'Riwayat Transaksi',
})[props.page] || 'Sirkulasi')

const showScanner = ref('')
function toggleScanner(id) {
  showScanner.value = showScanner.value === id ? '' : id
}

// ── TAB 1: PEMINJAMAN ──
const pinjam = reactive({
  qrToken: '', barcode: '', member: null, quota: 2,
  memberError: '', memberLoading: false,
  bookLoading: false, bookError: '', books: [],
  loanType: 'pembaca', submitting: false,
})

async function pinjamValidateMember() {
  if (!pinjam.qrToken) return
  pinjam.memberLoading = true; pinjam.memberError = ''; pinjam.member = null
  try {
    const res = await axios.post(route('loans.validate-member'), { qr_token: pinjam.qrToken })
    if (res.data.valid) { pinjam.member = res.data.member; pinjam.quota = res.data.quota ?? 2 }
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
  } catch (e) { pinjam.bookError = e.response?.data?.message || 'Buku tidak ditemukan.' }
  finally { pinjam.bookLoading = false }
}

function pinjamSubmit() {
  pinjam.submitting = true
  router.post(route('loans.store'), {
    qr_token: pinjam.qrToken, barcodes: pinjam.books.map(b => b.barcode), loan_type: pinjam.loanType,
  }, { onFinish: () => { pinjam.submitting = false } })
}

// ── TAB 2: PENGEMBALIAN ──
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
    if (res.data.on_loan) { retur.result = res.data }
    else { alert(res.data.message) }
  } catch (e) { alert(e.response?.data?.message || 'Buku tidak ditemukan.') }
  finally { retur.loading = false }
}

async function returProcess() {
  retur.processing = true
  try {
    const res = await axios.post(route('returns.store'), { barcode: retur.barcode, condition_after: retur.condition })
    retur.fineResult = res.data; retur.result = null
  } catch (e) { alert(e.response?.data?.message || 'Gagal memproses pengembalian.') }
  finally { retur.processing = false }
}

function returReset() { retur.barcode = ''; retur.result = null; retur.fineResult = null; retur.condition = '' }

// ── TAB 3: RIWAYAT ──
const riwayat = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  overdue: props.filters?.overdue || false,
})

function riwayatApply() {
  router.get(route('sirkulasi.riwayat'), riwayat, { preserveState: true, replace: true })
}

function riwayatExtend(loan) {
  if (!confirm(`Perpanjang pinjaman ${loan.loan_code}?`)) return
  router.post(route('loans.extend', loan.id))
}

// ── Helpers ──
function formatDate(d) { return d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }
function formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') }
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }
function isOverdue(loan) {
  if (!['aktif', 'diperpanjang'].includes(loan.status)) return false
  return new Date(loan.extended_due_date || loan.due_date) < new Date()
}
</script>

<style scoped>
.step-badge {
  width: 28px; height: 28px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: white; font-size: 12px; font-weight: 700;
  background: #6366f1;
}
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
