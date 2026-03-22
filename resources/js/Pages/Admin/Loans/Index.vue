<template>
  <AdminLayout title="Manajemen Peminjaman">
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <input v-model="filters.search" @keyup.enter="applyFilter" type="text" placeholder="Cari kode / nama anggota..." class="form-input py-1.5 w-64" />
        <select v-model="filters.status" @change="applyFilter" class="form-input py-1.5 w-40">
          <option value="">Semua Status</option>
          <option value="aktif">Aktif</option>
          <option value="diperpanjang">Diperpanjang</option>
          <option value="terlambat">Terlambat</option>
          <option value="selesai">Selesai</option>
        </select>
        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-600">
          <input v-model="filters.overdue" type="checkbox" @change="applyFilter" class="w-4 h-4 rounded" />
          Hanya Terlambat
        </label>
        <Link :href="route('loans.create')" class="btn btn-primary ml-2">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Pinjaman Baru
        </Link>
      </div>
    </template>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="font-semibold text-slate-800">Daftar Peminjaman</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ loans.total }} transaksi ditemukan</div>
        </div>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Kode Pinjam</th>
              <th>Anggota</th>
              <th>Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Jatuh Tempo</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="loan in loans.data" :key="loan.id">
              <td class="font-mono text-xs text-slate-500">{{ loan.loan_code }}</td>
              <td>
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    {{ loan.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-slate-800">{{ loan.member?.name }}</div>
                    <div class="text-xs text-slate-400">{{ loan.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="text-sm text-slate-600">{{ loan.items?.length }} buku</div>
                <div class="text-xs text-slate-400 truncate max-w-xs">
                  {{ loan.items?.map(i => i.copy?.book?.title).join(', ') }}
                </div>
              </td>
              <td class="text-sm text-slate-600">{{ formatDate(loan.loan_date) }}</td>
              <td>
                <span class="text-sm font-medium" :class="isOverdue(loan) ? 'text-red-600' : 'text-slate-600'">
                  {{ formatDate(loan.extended_due_date || loan.due_date) }}
                </span>
                <span v-if="isOverdue(loan)" class="block text-xs text-red-400">terlambat!</span>
              </td>
              <td><LoanBadge :status="loan.status" /></td>
              <td>
                <div class="flex gap-2">
                  <Link :href="route('loans.show', loan.id)" class="btn btn-sm btn-secondary">Detail</Link>
                  <button
                    v-if="['aktif','diperpanjang'].includes(loan.status)"
                    @click="extendLoan(loan)"
                    class="btn btn-sm btn-warning"
                  >
                    Perpanjang
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="loans.data.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-400">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-40"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div>Tidak ada data peminjaman</div>
                <Link :href="route('loans.create')" class="btn btn-primary mt-4 inline-flex">Buat Peminjaman Pertama</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="loans.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-sm text-slate-500">Halaman {{ loans.current_page }} dari {{ loans.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="loans.prev_page_url" :href="loans.prev_page_url" class="btn btn-sm btn-secondary">← Prev</Link>
          <Link v-if="loans.next_page_url" :href="loans.next_page_url" class="btn btn-sm btn-secondary">Next →</Link>
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
  overdue: props.filters?.overdue || false,
})

function applyFilter() {
  router.get(route('loans.index'), filters, { preserveState: true, replace: true })
}

function extendLoan(loan) {
  if (!confirm(`Perpanjang pinjaman ${loan.loan_code}?`)) return
  router.post(route('loans.extend', loan.id))
}

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
