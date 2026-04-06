<template>
  <AdminLayout title="Laporan Denda">
    <template #topbar-actions>
      <div class="flex gap-2">
        <button @click="exportPDF" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 hover:shadow-md">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          PDF
        </button>
        <button @click="exportExcel" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 hover:shadow-md">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Excel
        </button>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Subtitle -->
      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan data denda perpustakaan</p>
      </div>

      <!-- Period Filters -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1 gap-1">
            <button v-for="p in periods" :key="p.value"
              @click="changePeriod(p.value)"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                filters.period === p.value
                  ? 'bg-white dark:bg-gray-600 text-indigo-700 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
              ]">
              {{ p.label }}
            </button>
          </div>

          <!-- Custom date pickers -->
          <template v-if="filters.period === 'custom' || filters.period === 'hari'">
            <div class="flex items-center gap-2 ml-auto">
              <input type="date" v-model="filters.start_date"
                class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500" />
              <span class="text-gray-400 text-sm">s/d</span>
              <input type="date" v-model="filters.end_date"
                class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500" />
              <button @click="applyFilter" class="px-4 py-2 text-sm font-medium bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors">
                Terapkan
              </button>
            </div>
          </template>
        </div>
        <div class="mt-3 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Periode: {{ periodInfo.label }}
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-red-50 dark:bg-red-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Lunas</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-red-600">{{ formatRupiah(stats.total_belum_lunas) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ stats.count_belum_lunas }} denda</p>
          </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lunas</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ formatRupiah(stats.total_lunas) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ stats.count_lunas }} denda</p>
          </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-blue-50 dark:bg-blue-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibebaskan</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-blue-600">{{ formatRupiah(stats.total_dibebaskan) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ stats.count_dibebaskan }} denda</p>
          </div>
        </div>
      </div>

      <!-- Search & Status Filter -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-3 w-full md:w-auto">
          <select v-model="filters.status" @change="applyFilter"
            class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <option value="">Semua Status</option>
            <option value="belum_lunas">Belum Lunas</option>
            <option value="lunas">Lunas</option>
            <option value="dibebaskan">Dibebaskan</option>
          </select>
        </div>
        <div class="relative w-full md:w-96">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input v-model="filters.search" @keyup.enter="applyFilter" type="text"
            placeholder="Cari nama anggota..."
            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm">
        <table class="w-full min-w-[800px]">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-900/50">
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">No</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Anggota</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Buku</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Jenis</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Status</th>
              <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr v-for="(fine, i) in fines.data" :key="fine.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-gray-200">{{ (fines.from || 1) + i }}</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 shadow-sm">
                    {{ fine.loan_item?.loan?.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ fine.loan_item?.loan?.member?.name }}</div>
                    <div class="text-xs text-gray-400">{{ fine.loan_item?.loan?.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ fine.loan_item?.copy?.book?.title }}</td>
              <td class="px-5 py-4">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg"
                  :class="{
                    'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': fine.fine_type === 'keterlambatan',
                    'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': fine.fine_type === 'kerusakan',
                    'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400': fine.fine_type === 'kehilangan',
                  }">
                  {{ fineTypeLabel(fine.fine_type) }}
                </span>
              </td>
              <td class="px-5 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">{{ formatRupiah(fine.amount) }}</td>
              <td class="px-5 py-4">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg"
                  :class="{
                    'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400': fine.status === 'belum_lunas',
                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': fine.status === 'lunas',
                    'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': fine.status === 'dibebaskan',
                  }">
                  {{ statusLabel(fine.status) }}
                </span>
              </td>
              <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(fine.created_at) }}</td>
            </tr>
            <tr v-if="!fines.data || fines.data.length === 0">
              <td colspan="7" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center">
                  <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><path d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 dark:text-gray-500"/></svg>
                  </div>
                  <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data denda pada periode ini</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="fines.data && fines.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">
          Menampilkan <span class="font-semibold text-gray-800 dark:text-gray-200">{{ fines.from || 0 }}</span> sampai <span class="font-semibold text-gray-800 dark:text-gray-200">{{ fines.to || 0 }}</span> dari <span class="font-semibold text-gray-800 dark:text-gray-200">{{ fines.total }}</span> denda
        </div>
        <div v-if="fines.last_page > 1" class="flex gap-1.5 items-center">
          <template v-for="(link, idx) in fines.links" :key="idx">
            <Link v-if="link.url" :href="link.url"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
              :class="link.active ? 'bg-indigo-500 text-white border-indigo-500 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'"
              v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)" />
            <span v-else class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 text-sm border border-gray-200 dark:border-gray-700"
              v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)"></span>
          </template>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import * as XLSX from 'xlsx'

const props = defineProps({
  stats: Object,
  fines: Object,
  filters: Object,
  periodInfo: Object,
})

const periods = [
  { value: 'hari', label: 'Hari Ini' },
  { value: 'kemarin', label: 'Kemarin' },
  { value: 'minggu', label: 'Minggu Ini' },
  { value: 'bulan', label: 'Bulan Ini' },
  { value: 'custom', label: 'Custom' },
]

const filters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  period: props.filters?.period || 'hari',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

function changePeriod(period) {
  filters.period = period
  if (period !== 'custom' && period !== 'hari') {
    applyFilter()
  }
}

function applyFilter() {
  router.get(route('reports.fines'), {
    search: filters.search || undefined,
    status: filters.status || undefined,
    period: filters.period,
    start_date: filters.start_date || undefined,
    end_date: filters.end_date || undefined,
  }, { preserveState: true })
}

function formatRupiah(n) {
  return 'Rp ' + Number(n || 0).toLocaleString('id-ID')
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function fineTypeLabel(t) {
  return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t
}

function statusLabel(s) {
  return { belum_lunas: 'Belum Lunas', lunas: 'Lunas', dibebaskan: 'Dibebaskan' }[s] ?? s
}

function exportPDF() {
  const doc = new jsPDF()

  doc.setFontSize(16)
  doc.setFont(undefined, 'bold')
  doc.text('Laporan Denda Perpustakaan', 14, 20)

  doc.setFontSize(10)
  doc.setFont(undefined, 'normal')
  doc.text(`Periode: ${props.periodInfo.label}`, 14, 28)
  doc.text(`Dicetak: ${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`, 14, 34)

  // Summary
  doc.setFontSize(11)
  doc.setFont(undefined, 'bold')
  doc.text('Ringkasan:', 14, 44)
  doc.setFont(undefined, 'normal')
  doc.setFontSize(10)
  doc.text(`Belum Lunas: ${formatRupiah(props.stats.total_belum_lunas)} (${props.stats.count_belum_lunas} denda)`, 14, 51)
  doc.text(`Lunas: ${formatRupiah(props.stats.total_lunas)} (${props.stats.count_lunas} denda)`, 14, 57)
  doc.text(`Dibebaskan: ${formatRupiah(props.stats.total_dibebaskan)} (${props.stats.count_dibebaskan} denda)`, 14, 63)

  // Table
  const tableData = (props.fines.data || []).map((fine, i) => [
    i + 1,
    fine.loan_item?.loan?.member?.name || '-',
    fine.loan_item?.copy?.book?.title || '-',
    fineTypeLabel(fine.fine_type),
    formatRupiah(fine.amount),
    statusLabel(fine.status),
    formatDate(fine.created_at),
  ])

  doc.autoTable({
    startY: 70,
    head: [['No', 'Anggota', 'Buku', 'Jenis', 'Jumlah', 'Status', 'Tanggal']],
    body: tableData,
    styles: { fontSize: 8, cellPadding: 3 },
    headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [245, 247, 250] },
    columnStyles: {
      0: { cellWidth: 12 },
      4: { halign: 'right' },
    },
  })

  doc.save(`laporan-denda-${props.periodInfo.start}.pdf`)
}

function exportExcel() {
  const data = (props.fines.data || []).map((fine, i) => ({
    'No': i + 1,
    'Anggota': fine.loan_item?.loan?.member?.name || '-',
    'Kode Anggota': fine.loan_item?.loan?.member?.member_code || '-',
    'Buku': fine.loan_item?.copy?.book?.title || '-',
    'Jenis Denda': fineTypeLabel(fine.fine_type),
    'Jumlah (Rp)': fine.amount || 0,
    'Status': statusLabel(fine.status),
    'Tanggal': formatDate(fine.created_at),
  }))

  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Denda')

  // Auto column width
  const colWidths = Object.keys(data[0] || {}).map(key => ({
    wch: Math.max(key.length, ...data.map(row => String(row[key]).length)) + 2
  }))
  ws['!cols'] = colWidths

  XLSX.writeFile(wb, `laporan-denda-${props.periodInfo.start}.xlsx`)
}
</script>
