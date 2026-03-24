<template>
  <AdminLayout title="Dashboard">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="stat-card">
        <div class="stat-icon" style="background:#eef2ff">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke="#2b5a41" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ stats.total_anggota }}</div>
          <div class="text-sm text-slate-500 mt-0.5">Anggota Aktif</div>
          <div v-if="stats.pending_approval > 0" class="mt-1">
            <Link :href="route('members.index', { status: 'pending' })" class="text-xs text-indigo-500 font-medium hover:text-indigo-700">
              {{ stats.pending_approval }} menunggu verifikasi →
            </Link>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="#b45309" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ stats.active_loans }}</div>
          <div class="text-sm text-slate-500 mt-0.5">Pinjaman Aktif</div>
          <div v-if="stats.overdue_loans > 0" class="mt-1">
            <span class="text-xs text-red-500 font-medium">{{ stats.overdue_loans }} terlambat</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#dc2626" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ formatRupiah(stats.unpaid_fines) }}</div>
          <div class="text-sm text-slate-500 mt-0.5">Denda Belum Lunas</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v8.25m0 0A2.25 2.25 0 0 0 4.5 16.5h15a2.25 2.25 0 0 0 2.25-2.25V8.25m-16.5 8.25h16.5" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ stats.total_books }}</div>
          <div class="text-sm text-slate-500 mt-0.5">Judul Buku</div>
          <div class="text-xs text-slate-400 mt-0.5">Kunjungan hari ini: {{ stats.visits_today }}</div>
        </div>
      </div>
    </div>

    <!-- Bottom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Loans -->
      <div class="lg:col-span-2 card">
        <div class="card-header">
          <div>
            <div class="font-semibold text-slate-800">Peminjaman Terbaru</div>
            <div class="text-xs text-slate-400 mt-0.5">10 transaksi terakhir</div>
          </div>
          <Link :href="route('history.index')" class="text-sm text-indigo-500 font-medium hover:text-indigo-700">Lihat semua →</Link>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loan in recentLoans" :key="loan.id">
                <td>
                  <div class="font-medium text-slate-800">{{ loan.member?.name }}</div>
                  <div class="text-xs text-slate-400">{{ loan.loan_code }}</div>
                </td>
                <td>
                  <div class="text-xs text-slate-600">{{ loan.items?.length }} buku</div>
                </td>
                <td class="text-sm" :class="isOverdue(loan) ? 'text-red-600 font-semibold' : 'text-slate-600'">
                  {{ formatDate(loan.due_date) }}
                </td>
                <td><LoanBadge :status="loan.status" /></td>
              </tr>
              <tr v-if="recentLoans.length === 0">
                <td colspan="4" class="text-center text-slate-400 py-8">Belum ada data peminjaman</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pending Members + Overdue -->
      <div class="space-y-6">
        <!-- Pending Members -->
        <div class="card">
          <div class="card-header">
            <div class="font-semibold text-slate-800">Menunggu Persetujuan</div>
            <span v-if="pendingMembers.length > 0" class="badge badge-yellow">{{ pendingMembers.length }}</span>
          </div>
          <div class="divide-y divide-slate-50">
            <div v-for="member in pendingMembers" :key="member.id" class="px-5 py-3 flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-slate-800">{{ member.name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ member.type }} · {{ member.kelas?.name || '-' }}</div>
              </div>
              <div class="flex gap-2">
                <Link :href="route('members.show', member.id)" class="btn btn-sm btn-secondary">Lihat</Link>
              </div>
            </div>
            <div v-if="pendingMembers.length === 0" class="px-5 py-6 text-center text-sm text-slate-400">
              Tidak ada anggota pending ✓
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card card-body">
          <div class="font-semibold text-slate-800 mb-3">Aksi Cepat</div>
          <div class="space-y-2">
            <Link :href="route('loans.index')" class="btn btn-primary w-full justify-center">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Peminjaman Baru
            </Link>
            <Link :href="route('returns.index')" class="btn btn-secondary w-full justify-center">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Scan Pengembalian
            </Link>
            <Link :href="route('members.index', { status: 'pending' })" class="btn btn-secondary w-full justify-center">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Verifikasi Anggota
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import LoanBadge from '@/Components/LoanBadge.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  recentLoans: Array,
  overdueLoans: Array,
  pendingMembers: Array,
})

function formatRupiah(n) {
  if (n >= 1000000) return 'Rp ' + (n / 1000000).toFixed(1) + 'jt'
  if (n >= 1000)    return 'Rp ' + (n / 1000).toFixed(0) + 'rb'
  return 'Rp ' + (n || 0)
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function isOverdue(loan) {
  return ['aktif', 'diperpanjang'].includes(loan.status) && new Date(loan.due_date) < new Date()
}
</script>
