<template>
  <AdminLayout :title="`Peminjaman ${loan.loan_code}`">
    <div class="max-w-4xl space-y-6">
      <!-- Header Info -->
      <div class="card card-body">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div>
            <div class="text-xs text-slate-400 mb-1">Kode Pinjam</div>
            <div class="font-mono font-semibold text-slate-800">{{ loan.loan_code }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 mb-1">Anggota</div>
            <div class="font-semibold text-slate-800">{{ loan.member?.name }}</div>
            <div class="text-xs text-slate-400">{{ loan.member?.member_code }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 mb-1">Jatuh Tempo</div>
            <div class="font-semibold" :class="isOverdue ? 'text-red-600' : 'text-slate-800'">
              {{ formatDate(loan.extended_due_date || loan.due_date) }}
              <span v-if="isOverdue" class="badge badge-red ml-1">Terlambat</span>
            </div>
          </div>
          <div>
            <div class="text-xs text-slate-400 mb-1">Status</div>
            <LoanBadge :status="loan.status" />
          </div>
        </div>

        <div class="divider"></div>

        <div class="flex gap-3">
          <button
            v-if="['aktif','diperpanjang'].includes(loan.status)"
            @click="extendLoan"
            class="btn btn-warning"
          >
            Perpanjang Pinjaman
          </button>
          <Link :href="route('sirkulasi.pengembalian')" class="btn btn-secondary">
            Proses Pengembalian
          </Link>
          <Link :href="route('sirkulasi.riwayat')" class="btn btn-secondary ml-auto">
            ← Kembali
          </Link>
        </div>
      </div>

      <!-- Buku -->
      <div class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800">Detail Buku ({{ loan.items?.length }} item)</div>
        </div>
        <div class="divide-y divide-slate-50">
          <div v-for="item in loan.items" :key="item.id" class="px-6 py-4 flex items-start justify-between gap-4">
            <div>
              <div class="font-semibold text-slate-800">{{ item.copy?.book?.title }}</div>
              <div class="text-sm text-slate-500 mt-0.5">{{ item.copy?.book?.author }}</div>
              <div class="text-xs text-slate-400 mt-1 font-mono">{{ item.copy?.copy_code }} · {{ item.copy?.barcode }}</div>
            </div>
            <div class="text-right flex-shrink-0">
              <div v-if="item.returned_at">
                <span class="badge badge-green">Dikembalikan</span>
                <div class="text-xs text-slate-400 mt-1">{{ formatDate(item.returned_at) }}</div>
                <div class="text-xs mt-0.5" :class="item.condition_after === 'baik' ? 'text-green-600' : 'text-red-500'">
                  Kondisi: {{ item.condition_after }}
                </div>
              </div>
              <span v-else class="badge badge-yellow">Belum Dikembalikan</span>

              <!-- Fines -->
              <div v-if="item.fines?.length" class="mt-2 space-y-1">
                <div v-for="fine in item.fines" :key="fine.id" class="text-xs">
                  <span class="badge badge-red">Denda {{ fineLabel(fine.fine_type) }}: Rp {{ Number(fine.amount).toLocaleString('id-ID') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Riwayat Perpanjangan -->
      <div v-if="loan.extensions?.length" class="card">
        <div class="card-header"><div class="font-semibold text-slate-800">Riwayat Perpanjangan</div></div>
        <div class="table-wrapper">
          <table>
            <thead><tr><th>#</th><th>Jatuh Tempo Lama</th><th>Jatuh Tempo Baru</th><th>Oleh</th></tr></thead>
            <tbody>
              <tr v-for="(ext, i) in loan.extensions" :key="ext.id">
                <td class="text-sm text-slate-500">{{ i + 1 }}</td>
                <td class="text-sm text-slate-600">{{ formatDate(ext.old_due_date) }}</td>
                <td class="text-sm font-medium">{{ formatDate(ext.new_due_date) }}</td>
                <td class="text-sm text-slate-600">{{ ext.extended_by?.name }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import LoanBadge from '@/Components/LoanBadge.vue'

const props = defineProps({ loan: Object })

const isOverdue = computed(() => {
  if (!['aktif', 'diperpanjang'].includes(props.loan.status)) return false
  return new Date(props.loan.extended_due_date || props.loan.due_date) < new Date()
})

function extendLoan() {
  if (!confirm('Perpanjang masa pinjam?')) return
  router.post(route('loans.extend', props.loan.id))
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }
</script>
