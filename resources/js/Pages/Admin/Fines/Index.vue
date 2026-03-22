<template>
  <AdminLayout title="Manajemen Denda">
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <input v-model="filters.search" @keyup.enter="applyFilter" type="text" placeholder="Cari nama anggota..." class="form-input py-1.5 w-56" />
        <select v-model="filters.status" @change="applyFilter" class="form-input py-1.5 w-40">
          <option value="">Semua Status</option>
          <option value="belum_lunas">Belum Lunas</option>
          <option value="lunas">Lunas</option>
          <option value="dibebaskan">Dibebaskan</option>
        </select>
      </div>
    </template>

    <div class="card">
      <div class="card-header">
        <div class="font-semibold text-slate-800">Daftar Denda</div>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Anggota</th>
              <th>Buku</th>
              <th>Jenis Denda</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fine in fines.data" :key="fine.id">
              <td>
                <div class="font-medium text-slate-800">{{ fine.loan_item?.loan?.member?.name }}</div>
                <div class="text-xs text-slate-400">{{ fine.loan_item?.loan?.member?.member_code }}</div>
              </td>
              <td class="text-sm text-slate-600 max-w-xs truncate">{{ fine.loan_item?.copy?.book?.title }}</td>
              <td>
                <span class="badge" :class="fineTypeClass(fine.fine_type)">{{ fineTypeLabel(fine.fine_type) }}</span>
              </td>
              <td class="font-semibold text-slate-800">{{ formatRupiah(fine.amount) }}</td>
              <td><FineBadge :status="fine.status" /></td>
              <td class="text-sm text-slate-400">{{ formatDate(fine.created_at) }}</td>
              <td>
                <div v-if="fine.status === 'belum_lunas'" class="flex gap-2">
                  <button @click="openPayModal(fine)" class="btn btn-sm btn-success">Bayar</button>
                  <button @click="openFreeModal(fine)" class="btn btn-sm btn-secondary">Bebaskan</button>
                </div>
                <span v-else class="text-xs text-slate-400">—</span>
              </td>
            </tr>
            <tr v-if="fines.data.length === 0">
              <td colspan="7" class="text-center py-10 text-slate-400">Tidak ada data denda</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="fines.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex justify-between">
        <Link v-if="fines.prev_page_url" :href="fines.prev_page_url" class="btn btn-sm btn-secondary">← Prev</Link>
        <Link v-if="fines.next_page_url" :href="fines.next_page_url" class="btn btn-sm btn-secondary">Next →</Link>
      </div>
    </div>

    <!-- Pay Modal -->
    <div v-if="payModal.show" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5)">
      <div class="card w-full max-w-md mx-4">
        <div class="card-header">
          <div class="font-semibold text-slate-800">Bayar Denda</div>
          <button @click="payModal.show = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <div class="card-body space-y-4">
          <div class="p-3 rounded-xl" style="background:#fef2f2">
            <div class="text-sm text-slate-500">Total Denda</div>
            <div class="text-2xl font-bold text-red-600">{{ formatRupiah(payModal.fine?.amount) }}</div>
          </div>
          <div class="form-group">
            <label class="form-label">Jumlah Dibayar (Rp)</label>
            <input v-model="payForm.amount_paid" type="number" class="form-input" :max="payModal.fine?.amount" />
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Kwitansi (opsional)</label>
            <input v-model="payForm.receipt_number" type="text" class="form-input" placeholder="KWT-001" />
          </div>
          <div class="flex gap-3">
            <button @click="submitPay" :disabled="payForm.processing" class="btn btn-success flex-1 justify-center">
              {{ payForm.processing ? 'Menyimpan...' : 'Konfirmasi Bayar' }}
            </button>
            <button @click="payModal.show = false" class="btn btn-secondary">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import FineBadge from '@/Components/FineBadge.vue'

const props = defineProps({ fines: Object, filters: Object })
const filters = reactive({ search: props.filters?.search || '', status: props.filters?.status || '' })
const payModal = reactive({ show: false, fine: null })
const payForm  = useForm({ amount_paid: '', receipt_number: '' })

function applyFilter() { router.get(route('fines.index'), filters, { preserveState: true }) }
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
function fineTypeClass(t) { return { keterlambatan: 'badge-yellow', kerusakan: 'badge-orange', kehilangan: 'badge-red' }[t] ?? 'badge-gray' }
</script>
