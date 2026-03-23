<template>
  <AdminLayout title="Manajemen Denda">

    <div class="space-y-4 md:space-y-6">

      <!-- Rows per page, Filter & Search (separate card) -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Left: Rows per page -->
        <div class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
          <span>Tampilkan</span>
          <select v-model="filters.perPage" @change="applyFilter"
            class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span>baris</span>
        </div>

        <!-- Right: Status filter & Search -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
          <select v-model="filters.status" @change="applyFilter"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="belum_lunas">Belum Lunas</option>
            <option value="lunas">Lunas</option>
            <option value="dibebaskan">Dibebaskan</option>
          </select>

          <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input v-model="filters.search" @keyup.enter="applyFilter" type="text"
              placeholder="Cari nama anggota..."
              class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
          </div>
        </div>
      </div>

      <!-- Table (separate card) -->
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">No</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Anggota</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Buku</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Jenis Denda</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Jumlah</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Status</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Tanggal</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="fines.data && fines.data.length > 0" v-for="(fine, i) in fines.data" :key="fine.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900">{{ (fines.from || 1) + i }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600">
                    {{ fine.loan_item?.loan?.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ fine.loan_item?.loan?.member?.name }}</div>
                    <div class="text-xs text-gray-400">{{ fine.loan_item?.loan?.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-900 max-w-xs truncate">{{ fine.loan_item?.copy?.book?.title }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-amber-100 text-amber-700': fine.fine_type === 'keterlambatan',
                    'bg-orange-100 text-orange-700': fine.fine_type === 'kerusakan',
                    'bg-red-100 text-red-700': fine.fine_type === 'kehilangan',
                  }">
                  {{ fineTypeLabel(fine.fine_type) }}
                </span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-semibold text-gray-900">{{ formatRupiah(fine.amount) }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-red-100 text-red-700': fine.status === 'belum_lunas',
                    'bg-green-100 text-green-700': fine.status === 'lunas',
                    'bg-blue-100 text-blue-700': fine.status === 'dibebaskan',
                  }">
                  {{ fine.status === 'belum_lunas' ? 'Belum Lunas' : fine.status === 'lunas' ? 'Lunas' : 'Dibebaskan' }}
                </span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-500">{{ formatDate(fine.created_at) }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div v-if="fine.status === 'belum_lunas'" class="flex gap-2">
                  <button @click="openPayModal(fine)" class="px-3 py-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">Bayar</button>
                  <button @click="openFreeModal(fine)" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-700 font-medium">Bebaskan</button>
                </div>
                <span v-else class="text-xs text-gray-400">—</span>
              </td>
            </tr>
            <!-- Empty state -->
            <tr v-if="!fines.data || fines.data.length === 0">
              <td colspan="8" class="px-6 py-16 text-center">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 text-gray-300"><path d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="text-gray-500 font-medium">Tidak ada data denda</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (standalone, outside table card) -->
      <div v-if="fines.data && fines.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-semibold text-gray-800">{{ fines.from || 0 }}</span> sampai <span class="font-semibold text-gray-800">{{ fines.to || 0 }}</span> dari <span class="font-semibold text-gray-800">{{ fines.total }}</span> denda
        </div>
        <div v-if="fines.last_page > 1" class="flex gap-1.5 items-center">
          <template v-for="(link, idx) in fines.links" :key="idx">
            <Link v-if="link.url" :href="link.url"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
              :class="link.active ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
              v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)" />
            <span v-else class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 text-sm border border-gray-200" v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)"></span>
          </template>
        </div>
      </div>

    </div>

    <!-- Pay Modal -->
    <div v-if="payModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="payModal.show = false">
      <div class="w-full max-w-md bg-white rounded-2xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-xl font-bold text-gray-900">Bayar Denda</h3>
          <button @click="payModal.show = false" class="text-gray-400 hover:text-gray-600">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <div class="space-y-4">
          <div class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="text-sm text-gray-500">Total Denda</div>
            <div class="text-2xl font-bold text-red-600">{{ formatRupiah(payModal.fine?.amount) }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Dibayar (Rp)</label>
            <input v-model="payForm.amount_paid" type="number" :max="payModal.fine?.amount"
              class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kwitansi (opsional)</label>
            <input v-model="payForm.receipt_number" type="text" placeholder="KWT-001"
              class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900" />
          </div>
          <div class="flex gap-3 pt-2">
            <button @click="submitPay" :disabled="payForm.processing"
              class="flex-1 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg disabled:opacity-50 transition">
              {{ payForm.processing ? 'Menyimpan...' : 'Konfirmasi Bayar' }}
            </button>
            <button @click="payModal.show = false"
              class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'

const props = defineProps({ fines: Object, filters: Object })

const filters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  perPage: props.filters?.per_page || 10,
})

const payModal = reactive({ show: false, fine: null })
const payForm  = useForm({ amount_paid: '', receipt_number: '' })

function applyFilter() {
  router.get(route('fines.index'), {
    search: filters.search,
    status: filters.status,
    per_page: filters.perPage,
  }, { preserveState: true })
}

function openPayModal(fine) { payModal.fine = fine; payModal.show = true; payForm.amount_paid = fine.amount }
function submitPay() {
  payForm.post(route('fines.pay', payModal.fine.id), { onSuccess: () => { payModal.show = false } })
}
function openFreeModal(fine) {
  const reason = prompt('Alasan pembebasan denda:')
  if (reason) router.post(route('fines.free', fine.id), { reason })
}

function formatRupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID') }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) }
function fineTypeLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }
</script>
