<template>
  <AdminLayout title="Manajemen Peminjaman">
    <template #topbar-actions>
      <Link :href="route('loans.create')" class="btn py-2 btn-primary text-sm font-medium gap-1.5">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Pinjaman Baru
      </Link>
    </template>

    <div class="card bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden mt-6">
      
      <!-- Top Bar: Filters & Search -->
      <div class="px-6 py-5 flex flex-col xl:flex-row items-center justify-between border-b border-slate-100 gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <select v-model="filters.status" @change="applyFilter" class="form-input py-1.5 px-3 text-sm rounded-md border-slate-200 text-slate-700 bg-white min-w-[140px]">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="diperpanjang">Diperpanjang</option>
            <option value="terlambat">Terlambat</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>
        
        <div class="relative w-full xl:w-80 mt-2 xl:mt-0">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-slate-400">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </div>
          <input v-model="filters.search" @keyup.enter="applyFilter" type="text"
            placeholder="Cari kode / nama anggota..." class="form-input pl-10 pr-4 py-2 text-sm w-full border-slate-200 rounded-lg text-slate-700 placeholder-slate-400" />
        </div>
      </div>

      <!-- Table Section -->
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-widest">
              <th class="py-4 px-6 w-16 text-center">No</th>
              <th class="py-4 px-6">Kode Pinjam</th>
              <th class="py-4 px-6">Anggota</th>
              <th class="py-4 px-6">Buku</th>
              <th class="py-4 px-6">Tgl Pinjam</th>
              <th class="py-4 px-6">Jatuh Tempo</th>
              <th class="py-4 px-6 text-center">Status</th>
              <th class="py-4 px-6 text-center w-36">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="loans.data && loans.data.length > 0" class="divide-y divide-slate-100 bg-white">
            <tr v-for="(loan, i) in loans.data" :key="loan.id" class="hover:bg-slate-50/50 transition-colors">
              <td class="py-4 px-6 text-center text-sm font-medium text-slate-700">{{ loans.from + i }}</td>
              <td class="py-4 px-6 font-mono text-sm font-medium text-slate-500">{{ loan.loan_code }}</td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#10b981,#059669)">
                    {{ loan.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-semibold text-slate-800">{{ loan.member?.name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ loan.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm font-semibold text-slate-700">{{ loan.items?.length }} buku</div>
                <div class="text-xs text-slate-500 truncate max-w-[200px]" :title="loan.items?.map(i => i.copy?.book?.title).join(', ')">
                  {{ loan.items?.map(i => i.copy?.book?.title).join(', ') }}
                </div>
              </td>
              <td class="py-4 px-6 text-sm text-slate-600">{{ formatDate(loan.loan_date) }}</td>
              <td class="py-4 px-6">
                <span class="text-sm font-semibold" :class="isOverdue(loan) ? 'text-rose-500' : 'text-slate-700'">
                  {{ formatDate(loan.extended_due_date || loan.due_date) }}
                </span>
                <span v-if="isOverdue(loan)" class="block text-[11px] font-bold text-rose-400 uppercase tracking-wider mt-0.5">Terlambat!</span>
              </td>
              <td class="py-4 px-6 text-center"><LoanBadge :status="loan.status" /></td>
              <td class="py-4 px-6 text-center">
                <div class="flex items-center justify-center gap-3">
                  <button @click="openDetailModal(loan)" class="text-blue-500 hover:text-blue-700 font-medium text-sm hover:underline outline-none">Detail</button>
                  <button v-if="['aktif','diperpanjang'].includes(loan.status)" @click="extendLoan(loan)" class="text-amber-500 hover:text-amber-600 font-medium text-sm hover:underline outline-none">Perpanjang</button>
                </div>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="8" class="py-16 text-center">
                <div class="text-slate-400 mb-2">
                  <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-40"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="text-slate-600 font-medium mb-4">Tidak ada data peminjaman</div>
                <Link :href="route('loans.create')" class="btn btn-primary inline-flex">Buat Peminjaman Pertama</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div v-if="loans.data && loans.data.length > 0" class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
        <div class="text-sm text-slate-500">
           Menampilkan <span class="font-semibold text-slate-700">{{ loans.from || 0 }}</span> sampai <span class="font-semibold text-slate-700">{{ loans.to || 0 }}</span> dari <span class="font-semibold text-slate-700">{{ loans.total }}</span> transaksi
        </div>
        <div v-if="loans.last_page > 1" class="flex items-center gap-1.5">
          <Link v-if="loans.prev_page_url" :href="loans.prev_page_url"
            class="w-9 h-9 flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-white border text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800">
            ‹
          </Link>
          <span class="w-9 h-9 flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-emerald-500 text-white border border-emerald-500 shadow-sm">{{ loans.current_page }}</span>
          <span class="text-slate-400 mx-1">/</span>
          <span class="text-sm font-medium text-slate-500 mr-2">{{ loans.last_page }}</span>
          
          <Link v-if="loans.next_page_url" :href="loans.next_page_url"
            class="w-9 h-9 flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-white border text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800">
            ›
          </Link>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="detailModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="detailModal.show = false">
      <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-emerald-500 to-emerald-600">
              {{ detailModal.loan?.member?.name?.[0]?.toUpperCase() }}
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ detailModal.loan?.loan_code }}</h3>
              <p class="text-sm text-gray-500">{{ detailModal.loan?.member?.name }} · {{ detailModal.loan?.member?.member_code }}</p>
            </div>
          </div>
          <button @click="detailModal.show = false" class="text-gray-400 hover:text-gray-600">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Tgl Pinjam</div>
              <div class="text-sm font-semibold text-gray-900">{{ formatDate(detailModal.loan?.loan_date) }}</div>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Jatuh Tempo</div>
              <div class="text-sm font-semibold" :class="isOverdue(detailModal.loan) ? 'text-red-600' : 'text-gray-900'">{{ formatDate(detailModal.loan?.extended_due_date || detailModal.loan?.due_date) }}</div>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Status</div>
              <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                :class="{ 'bg-green-100 text-green-700': detailModal.loan?.status==='aktif', 'bg-amber-100 text-amber-700': detailModal.loan?.status==='diperpanjang', 'bg-red-100 text-red-700': detailModal.loan?.status==='terlambat', 'bg-gray-100 text-gray-500': detailModal.loan?.status==='selesai' }">{{ detailModal.loan?.status }}</span>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Jumlah Buku</div>
              <div class="text-sm font-semibold text-gray-900">{{ detailModal.loan?.items?.length || 0 }} item</div>
            </div>
          </div>
          <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Detail Buku</h4>
          <div class="space-y-3 mb-6">
            <div v-for="item in detailModal.loan?.items" :key="item.id" class="p-4 rounded-xl bg-white border border-gray-200 flex items-start justify-between gap-4">
              <div class="min-w-0 flex-1">
                <div class="font-semibold text-gray-900 truncate">{{ item.copy?.book?.title }}</div>
                <div class="text-sm text-gray-500 mt-0.5">{{ item.copy?.book?.author }}</div>
                <div class="text-xs text-gray-400 mt-1 font-mono">{{ item.copy?.copy_code }} · {{ item.copy?.barcode }}</div>
              </div>
              <div class="text-right flex-shrink-0">
                <div v-if="item.returned_at">
                  <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Dikembalikan</span>
                  <div class="text-xs text-gray-400 mt-1">{{ formatDate(item.returned_at) }}</div>
                  <div class="text-xs mt-0.5" :class="item.condition_after === 'baik' ? 'text-green-600' : 'text-red-500'">Kondisi: {{ item.condition_after }}</div>
                </div>
                <span v-else class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Belum Dikembalikan</span>
                <div v-if="item.fines?.length" class="mt-2 space-y-1">
                  <div v-for="fine in item.fines" :key="fine.id" class="text-xs">
                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Denda {{ fineLabel(fine.fine_type) }}: Rp {{ Number(fine.amount).toLocaleString('id-ID') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="detailModal.loan?.extensions?.length">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Riwayat Perpanjangan</h4>
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl">
              <table class="w-full"><thead class="bg-gray-100"><tr><th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">#</th><th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Jatuh Tempo Lama</th><th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Jatuh Tempo Baru</th><th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Oleh</th></tr></thead>
              <tbody class="divide-y divide-gray-100"><tr v-for="(ext, i) in detailModal.loan.extensions" :key="ext.id"><td class="px-4 py-2 text-sm text-gray-500">{{ i+1 }}</td><td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(ext.old_due_date) }}</td><td class="px-4 py-2 text-sm font-medium text-gray-900">{{ formatDate(ext.new_due_date) }}</td><td class="px-4 py-2 text-sm text-gray-600">{{ ext.extended_by?.name }}</td></tr></tbody></table>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
          <div class="flex gap-2">
            <button v-if="['aktif','diperpanjang'].includes(detailModal.loan?.status)" @click="extendLoan(detailModal.loan); detailModal.show = false" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg text-sm transition">Perpanjang</button>
          </div>
          <button @click="detailModal.show = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">Tutup</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import LoanBadge from '@/Components/LoanBadge.vue'

const props = defineProps({ loans: Object, filters: Object })

const filters = reactive({
  search:  props.filters?.search  || '',
  status:  props.filters?.status  || '',
})

function applyFilter() {
  router.get(route('loans.index'), filters, { preserveState: true, replace: true })
}

function extendLoan(loan) {
  if (!confirm(`Perpanjang pinjaman ${loan.loan_code}?`)) return
  router.post(route('loans.extend', loan.id))
}

const detailModal = reactive({ show: false, loan: null })
function openDetailModal(loan) { detailModal.loan = loan; detailModal.show = true }
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function isOverdue(loan) {
  if (!['aktif', 'diperpanjang'].includes(loan.status)) return false
  const due = new Date(loan.extended_due_date || loan.due_date)
  return due < new Date()
}
</script>
