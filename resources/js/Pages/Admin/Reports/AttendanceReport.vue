<template>
  <AdminLayout title="Laporan Presensi">
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
        <p class="text-sm text-gray-500 dark:text-gray-400">Statistik kunjungan perpustakaan</p>
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
          <template v-if="filters.period === 'custom'">
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
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kunjungan</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ stats.total_all }}</p>
            <p class="text-xs text-gray-400 mt-1">orang</p>
          </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-blue-50 dark:bg-blue-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengunjung</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-blue-600">{{ stats.total_pengunjung }}</p>
            <p class="text-xs text-gray-400 mt-1">orang</p>
          </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembaca</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ stats.total_pembaca }}</p>
            <p class="text-xs text-gray-400 mt-1">orang</p>
          </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm">
          <div class="absolute top-0 right-0 w-24 h-24 transform translate-x-6 -translate-y-6 bg-amber-50 dark:bg-amber-900/20 rounded-full"></div>
          <div class="relative">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Harian</p>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ stats.avg_daily }}</p>
            <p class="text-xs text-gray-400 mt-1">orang/hari</p>
          </div>
        </div>
      </div>

      <!-- Line Chart -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Grafik Presensi</h3>
            <p class="text-sm text-gray-400 mt-0.5">Perbandingan pengunjung dan pembaca</p>
          </div>
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-blue-500"></span>
              <span class="text-sm text-gray-600 dark:text-gray-300">Pengunjung</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
              <span class="text-sm text-gray-600 dark:text-gray-300">Pembaca</span>
            </div>
          </div>
        </div>
        <div v-if="lineChartData?.labels?.length > 0" class="relative" style="height: 350px">
          <Line :key="chartKey" :data="lineChartData" :options="lineChartOptions" />
        </div>
        <div v-else class="relative flex items-center justify-center" style="height: 350px">
          <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
              <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 dark:text-gray-500"/></svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data presensi pada periode ini</p>
          </div>
        </div>
      </div>

      <!-- Daily Summary Table -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ringkasan Harian</h3>
          <p class="text-sm text-gray-400 mt-0.5">Detail kunjungan per tanggal</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 dark:bg-gray-900/50">
                <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-left text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-center text-gray-500 dark:text-gray-400 uppercase">
                  <div class="flex items-center justify-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    Pengunjung
                  </div>
                </th>
                <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-center text-gray-500 dark:text-gray-400 uppercase">
                  <div class="flex items-center justify-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Pembaca
                  </div>
                </th>
                <th class="px-5 py-3.5 text-xs font-bold tracking-wider text-center text-gray-500 dark:text-gray-400 uppercase">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr v-for="(day, i) in dailySummary" :key="i" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-gray-200">{{ day.date }}</td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 text-sm font-semibold text-blue-700 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg">
                    {{ day.pengunjung }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 text-sm font-semibold text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-lg">
                    {{ day.pembaca }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 text-sm font-bold text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    {{ day.total }}
                  </span>
                </td>
              </tr>
              <tr v-if="dailySummary.length === 0">
                <td colspan="4" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                      <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 dark:text-gray-500"/></svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data presensi pada periode ini</p>
                  </div>
                </td>
              </tr>
            </tbody>
            <!-- Footer totals -->
            <tfoot v-if="dailySummary.length > 0">
              <tr class="bg-gray-50 dark:bg-gray-900/50 border-t-2 border-gray-200 dark:border-gray-600">
                <td class="px-5 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">Total</td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-1.5 text-sm font-bold text-blue-700 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-lg">
                    {{ stats.total_pengunjung }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-1.5 text-sm font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-900/50 dark:text-emerald-300 rounded-lg">
                    {{ stats.total_pembaca }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-1.5 text-sm font-bold text-white bg-indigo-500 rounded-lg shadow-sm">
                    {{ stats.total_all }}
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import * as XLSX from 'xlsx'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
)

const props = defineProps({
  stats: Object,
  chartData: Object,
  dailySummary: Array,
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
  period: props.filters?.period || 'minggu',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
})

function changePeriod(period) {
  filters.period = period
  if (period !== 'custom') {
    applyFilter()
  }
}

function applyFilter() {
  router.get(route('reports.attendance'), {
    period: filters.period,
    start_date: filters.start_date || undefined,
    end_date: filters.end_date || undefined,
  }, { preserveState: true })
}

// Chart configuration
const lineChartData = computed(() => ({
  labels: props.chartData?.labels || [],
  datasets: [
    {
      label: 'Pengunjung',
      data: props.chartData?.pengunjung || [],
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.08)',
      borderWidth: 3,
      pointBackgroundColor: '#3b82f6',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2,
      pointRadius: 5,
      pointHoverRadius: 8,
      pointHoverBackgroundColor: '#3b82f6',
      pointHoverBorderColor: '#ffffff',
      pointHoverBorderWidth: 3,
      tension: 0.4,
      fill: true,
    },
    {
      label: 'Pembaca',
      data: props.chartData?.pembaca || [],
      borderColor: '#10b981',
      backgroundColor: 'rgba(16, 185, 129, 0.08)',
      borderWidth: 3,
      pointBackgroundColor: '#10b981',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2,
      pointRadius: 5,
      pointHoverRadius: 8,
      pointHoverBackgroundColor: '#10b981',
      pointHoverBorderColor: '#ffffff',
      pointHoverBorderWidth: 3,
      tension: 0.4,
      fill: true,
    },
  ],
}))

// Generate a unique key for chart re-rendering when data changes
const chartKey = computed(() => {
  return `chart-${props.periodInfo?.start}-${props.chartData?.labels?.length || 0}`
})

const lineChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      backgroundColor: 'rgba(15, 23, 42, 0.9)',
      titleColor: '#f1f5f9',
      bodyColor: '#e2e8f0',
      borderColor: 'rgba(148, 163, 184, 0.2)',
      borderWidth: 1,
      cornerRadius: 12,
      padding: 14,
      titleFont: { size: 13, weight: 'bold' },
      bodyFont: { size: 12 },
      bodySpacing: 6,
      usePointStyle: true,
      callbacks: {
        label: (context) => {
          return ` ${context.dataset.label}: ${context.parsed.y} orang`
        },
      },
    },
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
      ticks: {
        color: '#94a3b8',
        font: { size: 12 },
      },
      border: {
        display: false,
      },
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(148, 163, 184, 0.1)',
      },
      ticks: {
        color: '#94a3b8',
        font: { size: 12 },
        stepSize: 1,
        callback: (value) => {
          if (Number.isInteger(value)) return value
        },
      },
      border: {
        display: false,
      },
    },
  },
}))

function exportPDF() {
  const doc = new jsPDF()

  doc.setFontSize(16)
  doc.setFont(undefined, 'bold')
  doc.text('Laporan Presensi Perpustakaan', 14, 20)

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
  doc.text(`Total Kunjungan: ${props.stats.total_all} orang`, 14, 51)
  doc.text(`Pengunjung: ${props.stats.total_pengunjung} orang`, 14, 57)
  doc.text(`Pembaca: ${props.stats.total_pembaca} orang`, 14, 63)
  doc.text(`Rata-rata Harian: ${props.stats.avg_daily} orang/hari`, 14, 69)

  // Table
  const tableData = (props.dailySummary || []).map(day => [
    day.date,
    day.pengunjung,
    day.pembaca,
    day.total,
  ])

  // Add totals row
  tableData.push([
    'TOTAL',
    props.stats.total_pengunjung,
    props.stats.total_pembaca,
    props.stats.total_all,
  ])

  doc.autoTable({
    startY: 76,
    head: [['Tanggal', 'Pengunjung', 'Pembaca', 'Total']],
    body: tableData,
    styles: { fontSize: 9, cellPadding: 4, halign: 'center' },
    headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [245, 247, 250] },
    columnStyles: {
      0: { halign: 'left' },
    },
    didParseCell: (data) => {
      // Bold the total row
      if (data.row.index === tableData.length - 1) {
        data.cell.styles.fontStyle = 'bold'
        data.cell.styles.fillColor = [238, 242, 255]
      }
    },
  })

  doc.save(`laporan-presensi-${props.periodInfo.start}.pdf`)
}

function exportExcel() {
  const data = (props.dailySummary || []).map(day => ({
    'Tanggal': day.date,
    'Pengunjung': day.pengunjung,
    'Pembaca': day.pembaca,
    'Total': day.total,
  }))

  // Add totals row
  data.push({
    'Tanggal': 'TOTAL',
    'Pengunjung': props.stats.total_pengunjung,
    'Pembaca': props.stats.total_pembaca,
    'Total': props.stats.total_all,
  })

  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Presensi')

  // Auto column width
  ws['!cols'] = [
    { wch: 15 },
    { wch: 14 },
    { wch: 12 },
    { wch: 10 },
  ]

  XLSX.writeFile(wb, `laporan-presensi-${props.periodInfo.start}.xlsx`)
}
</script>
