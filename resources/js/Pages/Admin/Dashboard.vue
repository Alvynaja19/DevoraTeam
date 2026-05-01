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

    <!-- Chart Peminjaman -->
    <div class="card mb-6">
      <div class="card-header">
        <div>
          <div class="font-semibold text-slate-800 flex items-center gap-2">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" stroke="#2b5a41" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tren Peminjaman
          </div>
          <div class="text-xs text-slate-400 mt-0.5">{{ chartPeriodLabel }}</div>
        </div>
        <div class="flex items-center gap-3">
          <!-- Filter Tabs -->
          <div class="chart-filter-tabs">
            <button v-for="p in periods" :key="p.key"
              @click="activePeriod = p.key; nextTick(drawChart)"
              :class="['chart-filter-btn', activePeriod === p.key ? 'active' : '']">
              {{ p.label }}
            </button>
          </div>
          <div class="text-right">
            <div class="text-sm font-semibold text-slate-800">{{ loanSummary.this_month }} <span class="text-xs font-normal text-slate-400">bulan ini</span></div>
            <div class="flex items-center gap-1 justify-end">
              <span v-if="monthTrend >= 0" class="text-xs text-emerald-600 font-medium">▲ {{ Math.abs(monthTrend) }}</span>
              <span v-else class="text-xs text-red-500 font-medium">▼ {{ Math.abs(monthTrend) }}</span>
              <span class="text-xs text-slate-400">vs bln lalu</span>
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 pb-5 pt-2">
        <!-- Tooltip -->
        <div v-if="tooltip.visible" class="chart-tooltip" :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }">
          <div class="font-semibold text-white">{{ tooltip.label }}</div>
          <div class="text-emerald-300 text-sm">{{ tooltip.value }} transaksi</div>
        </div>
        <div class="chart-wrapper" ref="chartWrapper">
          <canvas ref="chartCanvas" class="loan-chart" @mousemove="onChartHover" @mouseleave="tooltip.visible = false"></canvas>
        </div>
        <!-- X axis labels -->
        <div class="flex justify-between mt-2 px-1">
          <span v-for="(item, i) in xLabels" :key="i" class="text-[10px] text-slate-400">{{ item }}</span>
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

      <!-- Right Panel -->
      <div class="space-y-6">
        <!-- Popular Books -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="font-semibold text-slate-800 flex items-center gap-2">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Buku Terpopuler Minggu Ini
              </div>
            </div>
          </div>
          <div class="divide-y divide-slate-50">
            <div v-for="(book, index) in popularBooks" :key="book.id" class="px-5 py-3 flex items-center gap-4">
              <div class="w-8 h-8 rounded bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-sm shrink-0">
                #{{ index + 1 }}
              </div>
              <div class="w-10 h-10 rounded shrink-0 bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                <img v-if="book.cover_image" :src="book.cover_image" class="w-full h-full object-cover">
                <svg v-else width="20" height="20" fill="none" viewBox="0 0 24 24" class="text-slate-400"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-800 truncate">{{ book.title }}</div>
                <div class="text-xs text-slate-500 truncate">{{ book.author || 'Author tidak diketahui' }}</div>
              </div>
              <div class="shrink-0 text-right">
                <div class="text-xs font-bold text-slate-700">{{ book.borrow_count }}</div>
                <div class="text-[10px] text-slate-400">kali dipinjam</div>
              </div>
            </div>

            <div v-if="popularBooks.length === 0" class="px-5 py-6 text-center text-sm text-slate-400">
              Belum ada peminjaman minggu ini.
            </div>
          </div>
        </div>

        <!-- Overdue Loans -->
        <div class="card">
          <div class="card-header">
            <div class="font-semibold text-slate-800">Pinjaman Terlambat</div>
            <span v-if="overdueLoans.length > 0" class="badge badge-red">{{ overdueLoans.length }}</span>
          </div>
          <div class="divide-y divide-slate-50">
            <div v-for="loan in overdueLoans" :key="loan.id" class="px-5 py-3 flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-slate-800">{{ loan.member?.name }}</div>
                <div class="text-xs text-red-500 mt-0.5">Jatuh tempo: {{ formatDate(loan.due_date) }}</div>
              </div>
              <Link :href="route('history.index', { search: loan.member?.name, status: 'terlambat' })" class="btn btn-sm btn-secondary">Lihat</Link>
            </div>
            <div v-if="overdueLoans.length === 0" class="px-5 py-6 text-center text-sm text-slate-400">
              Tidak ada pinjaman terlambat ✓
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
            <Link :href="route('members.index')" class="btn btn-secondary w-full justify-center">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Kelola Anggota
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import LoanBadge from '@/Components/LoanBadge.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  recentLoans: Array,
  overdueLoans: Array,
  popularBooks: Array,
  chartWeek:  Array,
  chartMonth: Array,
  chartYear:  Array,
  loanSummary: Object,
})

// ── Chart ──────────────────────────────────────────────
const chartCanvas = ref(null)
const chartWrapper = ref(null)
const tooltip = ref({ visible: false, x: 0, y: 0, label: '', value: 0 })
let barRects = []

const periods = [
  { key: 'week',  label: 'Minggu' },
  { key: 'month', label: 'Bulan'  },
  { key: 'year',  label: 'Tahun'  },
]
const activePeriod = ref('month')

const activeData = computed(() => {
  if (activePeriod.value === 'week')  return props.chartWeek  ?? []
  if (activePeriod.value === 'year')  return props.chartYear  ?? []
  return props.chartMonth ?? []
})

const chartPeriodLabel = computed(() => {
  if (activePeriod.value === 'week')  return '7 hari terakhir'
  if (activePeriod.value === 'year')  return '12 bulan terakhir'
  return '30 hari terakhir'
})

const maxVal = computed(() => Math.max(...activeData.value.map(d => d.total), 1))

const xLabels = computed(() => {
  const data = activeData.value
  const step = Math.ceil(data.length / 6)
  return data.filter((_, i) => i % step === 0 || i === data.length - 1).map(d => d.label)
})

const monthTrend = computed(() => (props.loanSummary?.this_month ?? 0) - (props.loanSummary?.last_month ?? 0))

function drawChart() {
  const canvas = chartCanvas.value
  if (!canvas || !activeData.value.length) return

  const wrapper = chartWrapper.value
  canvas.width  = wrapper.clientWidth
  canvas.height = 180

  const ctx   = canvas.getContext('2d')
  const data  = activeData.value
  const W     = canvas.width
  const H     = canvas.height
  const padL  = 8
  const padR  = 8
  const padT  = 16
  const padB  = 24
  const chartW = W - padL - padR
  const chartH = H - padT - padB
  const max   = maxVal.value

  ctx.clearRect(0, 0, W, H)
  barRects = []

  const n    = data.length
  const gap  = 3
  const barW = Math.max(4, (chartW - gap * (n - 1)) / n)

  // Grid lines
  const gridCount = 4
  ctx.strokeStyle = '#f1f5f9'
  ctx.lineWidth   = 1
  for (let g = 0; g <= gridCount; g++) {
    const y = padT + (chartH / gridCount) * g
    ctx.beginPath()
    ctx.moveTo(padL, y)
    ctx.lineTo(W - padR, y)
    ctx.stroke()
  }

  // Gradient fill
  const grad = ctx.createLinearGradient(0, padT, 0, padT + chartH)
  grad.addColorStop(0, 'rgba(43,90,65,0.85)')
  grad.addColorStop(1, 'rgba(43,90,65,0.15)')

  // Draw bars
  data.forEach((item, i) => {
    const barH = max > 0 ? (item.total / max) * chartH : 0
    const x    = padL + i * (barW + gap)
    const y    = padT + chartH - barH

    // Rounded top bar
    const r = Math.min(4, barW / 2, barH / 2)
    ctx.fillStyle = grad
    ctx.beginPath()
    if (barH > 0) {
      ctx.moveTo(x + r, y)
      ctx.lineTo(x + barW - r, y)
      ctx.arcTo(x + barW, y, x + barW, y + r, r)
      ctx.lineTo(x + barW, y + barH)
      ctx.lineTo(x, y + barH)
      ctx.lineTo(x, y + r)
      ctx.arcTo(x, y, x + r, y, r)
      ctx.closePath()
      ctx.fill()
    }
    barRects.push({ x, y, w: barW, h: barH, item })
  })
}

function onChartHover(e) {
  const canvas = chartCanvas.value
  if (!canvas) return
  const rect = canvas.getBoundingClientRect()
  const mx = e.clientX - rect.left
  const my = e.clientY - rect.top

  let found = false
  for (const bar of barRects) {
    if (mx >= bar.x && mx <= bar.x + bar.w) {
      tooltip.value = {
        visible: true,
        x: bar.x + bar.w / 2 - 48,
        y: Math.max(0, bar.y - 52),
        label: bar.item.label,
        value: bar.item.total,
      }
      // Redraw dengan highlight
      drawChart()
      const ctx = canvas.getContext('2d')
      ctx.fillStyle = 'rgba(43,90,65,1)'
      const r = Math.min(4, bar.w / 2)
      if (bar.h > 0) {
        ctx.beginPath()
        ctx.moveTo(bar.x + r, bar.y)
        ctx.lineTo(bar.x + bar.w - r, bar.y)
        ctx.arcTo(bar.x + bar.w, bar.y, bar.x + bar.w, bar.y + r, r)
        ctx.lineTo(bar.x + bar.w, bar.y + bar.h)
        ctx.lineTo(bar.x, bar.y + bar.h)
        ctx.lineTo(bar.x, bar.y + r)
        ctx.arcTo(bar.x, bar.y, bar.x + r, bar.y, r)
        ctx.closePath()
        ctx.fill()
      }
      found = true
      break
    }
  }
  if (!found) tooltip.value.visible = false
}

// Redraw saat filter berubah
watch(activePeriod, () => nextTick(drawChart))

onMounted(() => {
  nextTick(() => {
    drawChart()
    window.addEventListener('resize', drawChart)
  })
})

// ── Helpers ────────────────────────────────────────────
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

<style scoped>
.chart-wrapper {
  position: relative;
  width: 100%;
}
.loan-chart {
  display: block;
  width: 100%;
  cursor: crosshair;
  border-radius: 8px;
}
.chart-tooltip {
  position: absolute;
  background: #1e293b;
  border-radius: 8px;
  padding: 6px 12px;
  pointer-events: none;
  z-index: 10;
  white-space: nowrap;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
  font-size: 12px;
}
/* Filter tabs */
.chart-filter-tabs {
  display: flex;
  gap: 4px;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 3px;
}
.chart-filter-btn {
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  border: none;
  cursor: pointer;
  background: transparent;
  transition: all 0.15s ease;
}
.chart-filter-btn:hover {
  color: #2b5a41;
}
.chart-filter-btn.active {
  background: #fff;
  color: #2b5a41;
  font-weight: 600;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}
</style>
