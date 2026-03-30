<template>
  <AdminLayout title="Presensi Pengunjung">
    <div class="space-y-4 md:space-y-6">

      <!-- Scanner Section -->
      <div class="card bg-white shadow-xl rounded-2xl overflow-hidden border-0 ring-1 ring-slate-100">

        <!-- STEP 1: Scan Barcode -->
        <div v-if="!scannedMember">
          <div class="card-header">
            <div class="flex items-center gap-3">
              <div class="step-badge">1</div>
              <div class="font-semibold text-slate-800 dark:text-white">Scan QR Anggota</div>
            </div>
          </div>
          <div class="card-body">
            <div class="flex gap-2">
              <input 
                ref="barcodeInput" 
                v-model="form.barcode" 
                @keyup.enter="checkMember"
                type="text" 
                class="form-input flex-1" 
                placeholder="Scan QR code atau ketik kode anggota..." 
                autofocus 
                :disabled="loading"
              />
              <button @click="toggleScanner('visit-barcode')" type="button"
                :class="['btn', showScanner === 'visit-barcode' ? 'btn-secondary' : 'btn-outline']">
                📷 {{ showScanner === 'visit-barcode' ? 'Tutup' : 'Kamera' }}
              </button>
              <button @click="checkMember" :disabled="!form.barcode || loading" class="btn btn-primary">
                <span v-if="loading" class="spinner"></span>
                <span v-else>Validasi</span>
              </button>
            </div>

            <!-- Camera Popup Module -->
            <div class="mt-4 max-w-md mx-auto">
              <CameraScanner type="qr" :active="showScanner === 'visit-barcode'"
                @scanned="v => { form.barcode = v; showScanner = ''; checkMember(); }"
                @close="showScanner = ''; barcodeInput?.focus()" />
            </div>
          </div>
        </div>

        <!-- STEP 2: Profil & Kategori -->
        <div v-else>
          <div class="card-header border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-3">
              <div class="step-badge">2</div>
              <div class="font-semibold text-slate-800 dark:text-slate-200">Profil & Tujuan Kunjungan</div>
            </div>
          </div>
          <div class="card-body space-y-6">
            <!-- Member Profile Card -->
            <div class="p-4 bg-white rounded-2xl border-2 border-emerald-100 flex flex-col md:flex-row items-center justify-between gap-4">
              <div class="flex items-center gap-4 text-left w-full">
                <img v-if="scannedMember.photo" :src="`/storage/${scannedMember.photo}`" class="w-16 h-16 rounded-full object-cover shadow-sm bg-white">
                <div v-else class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-2xl shadow-sm">
                  {{ (scannedMember.name || '?').substring(0,2).toUpperCase() }}
                </div>
                <div>
                  <h3 class="text-lg font-bold text-slate-800">{{ scannedMember.name }}</h3>
                  <p class="text-sm font-semibold text-slate-500">
                    {{ scannedMember.nis_nip || scannedMember.member_code }} • 
                    <span v-if="scannedMember.type === 'siswa'">Siswa {{ scannedMember.kelas?.name ? ' - ' + scannedMember.kelas.name : '' }}</span>
                    <span v-else-if="scannedMember.type === 'guru'">Guru / Karyawan</span>
                    <span v-else-if="scannedMember.type === 'umum'">Umum</span>
                    <span v-else>Anggota</span>
                  </p>
                </div>
              </div>
              <button @click="cancelScan" type="button" class="btn btn-outline whitespace-nowrap border-slate-200 text-slate-600 hover:bg-slate-100 px-4 py-2 font-semibold w-full md:w-auto">
                Batal (X)
              </button>
            </div>

            <!-- Action Buttons (Pilih Kategori) -->
            <div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                <button type="button" @click="submitVisit('membaca')" :disabled="form.processing"
                  class="group relative p-6 rounded-2xl border-2 transition-all text-center border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                  <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-3 bg-emerald-100 text-emerald-600 group-hover:scale-110 transition-transform">
                    <span class="text-2xl">📖</span>
                  </div>
                  <h3 class="font-bold text-slate-800">Membaca Buku</h3>
                  <p class="text-xs mt-1 text-slate-500">Hanya membaca di tempat</p>
                </button>

                <button type="button" @click="submitVisit('pengunjung')" :disabled="form.processing"
                  class="group relative p-6 rounded-2xl border-2 transition-all text-center border-slate-200 hover:border-blue-500 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                  <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-3 bg-blue-100 text-blue-600 group-hover:scale-110 transition-transform">
                    <span class="text-2xl">🚶</span>
                  </div>
                  <h3 class="font-bold text-slate-800">Kunjungan</h3>
                  <p class="text-xs mt-1 text-slate-500">Mengerjakan tugas / internet</p>
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-4 rounded-xl flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-xl">👥</div>
          <div>
            <div class="text-2xl font-bold text-slate-800">{{ stats.total_today }}</div>
            <div class="text-xs font-semibold text-slate-500 uppercase">
              Total {{ filterForm.period === 'minggu' ? 'Minggu Ini' : (filterForm.period === 'bulan' ? 'Bulan Ini' : (filterForm.period === 'tahun' ? 'Tahun Ini' : 'Hari Ini')) }}
            </div>
          </div>
        </div>
        <div class="card p-4 rounded-xl flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-xl">📖</div>
          <div>
            <div class="text-2xl font-bold text-emerald-600">{{ stats.reading }}</div>
            <div class="text-xs font-semibold text-slate-500 uppercase">Membaca</div>
          </div>
        </div>
        <div class="card p-4 rounded-xl flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl">🚶</div>
          <div>
            <div class="text-2xl font-bold text-blue-600">{{ stats.visitor }}</div>
            <div class="text-xs font-semibold text-slate-500 uppercase">Kunjungan</div>
          </div>
        </div>
      </div>

      <!-- Recent Visits (Style disamakan dengan History/Index.vue) -->
      <div class="mt-8 flex flex-col md:flex-row justify-between items-center mb-4">
        <h3 class="font-bold text-slate-800 text-lg">Catatan Pengunjung (Hari Ini)</h3>
      </div>
      
      <!-- Filter & Search Card -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between mb-4">
        <!-- Left: Rows per page -->
        <div class="flex items-center gap-4 text-sm text-gray-600 whitespace-nowrap">
          <div class="flex items-center gap-2">
            <span>Tampilkan</span>
          <select v-model="filterForm.perPage" @change="applyFilters"
            class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span>baris</span>
          </div>
        </div>

        <!-- Right: Status filter & Search -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
          <select v-model="filterForm.period" @change="applyFilters"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="hari">Hari Ini</option>
            <option value="minggu">Minggu Ini</option>
            <option value="bulan">Bulan Ini</option>
            <option value="tahun">Tahun Ini</option>
          </select>

          <select v-model="filterForm.category" @change="applyFilters"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Tujuan</option>
            <option value="membaca">Membaca Buku</option>
            <option value="pengunjung">Kunjungan</option>
          </select>

          <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input v-model="filterForm.search" @keyup.enter="applyFilters" type="text"
              placeholder="Cari kode / NIS / nama anggota..."
              class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
          </div>
        </div>
      </div>
      
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm mb-6">
        <table class="w-full min-w-[800px] text-left">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">No</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Visitor</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Waktu / Jam</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Tujuan</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-right text-gray-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <template v-if="visits && visits.data && visits.data.length > 0">
              <tr v-for="(visit, i) in visits.data" :key="visit.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900">{{ (visits.from || 1) + i }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex items-center gap-3">
                  <!-- Profil circle fallback (seperti riwayat pinjaman) -->
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600">
                    {{ visit.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ visit.member?.name }}</div>
                    <div class="text-xs text-gray-400">{{ visit.member?.nis_nip || visit.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="text-sm font-semibold text-gray-700">{{ visit.visit_time }}</span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                      :class="{
                        'bg-emerald-100 text-emerald-700': visit.category === 'membaca',
                        'bg-blue-100 text-blue-700': visit.category === 'pengunjung'
                      }">
                  {{ visit.category }}
                </span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openEditModal(visit)" class="px-3 py-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">Edit</button>
                  <button @click="confirmDelete(visit)" class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-medium">Hapus</button>
                </div>
              </td>
            </tr>
            </template>
            <!-- Empty state row -->
            <tr v-else>
              <td colspan="5" class="px-6 py-16 text-center">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 text-gray-300"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="text-gray-500 font-medium">Belum ada catatan pengunjung hari ini.</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="visits && visits.data && visits.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-semibold text-gray-800">{{ visits.from || 0 }}</span> sampai <span class="font-semibold text-gray-800">{{ visits.to || 0 }}</span> dari <span class="font-semibold text-gray-800">{{ visits.total }}</span> kunjungan
        </div>
        <div v-if="visits.last_page > 1" class="flex gap-1.5 items-center">
          <template v-for="(link, idx) in visits.links" :key="idx">
            <Link v-if="link.url" :href="link.url"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
              :class="link.active ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
              v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)" />
            <span v-else class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 text-sm border border-gray-200" v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)"></span>
          </template>
        </div>
      </div>

    </div>

    <!-- Modal Form (Edit Tujuan Kunjungan) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-900">Edit Tujuan Kunjungan</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form @submit.prevent="saveVisitParams" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Anggota</label>
            <input type="text" class="form-input w-full bg-gray-100 text-gray-500" disabled :value="editingVisit?.member?.name + ' (' + (editingVisit?.member?.member_code || '') + ')'">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
              <input type="date" class="form-input w-full bg-gray-100 text-gray-500" disabled :value="editingVisit?.visit_date?.substring(0, 10)">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Jam</label>
              <input type="time" class="form-input w-full bg-gray-100 text-gray-500" disabled :value="editingVisit?.visit_time?.substring(0, 5)">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kunjungan</label>
            <select v-model="editForm.category" class="form-input w-full" required :disabled="editForm.processing">
              <option value="membaca">Membaca Buku</option>
              <option value="pengunjung">Kunjungan</option>
            </select>
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
            <button type="button" @click="closeModal" class="btn btn-outline text-gray-600 border-gray-300">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="editForm.processing">
              {{ editForm.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 dark:bg-slate-800">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f43f5e" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
          </div>
          <div>
            <h3 class="font-semibold text-slate-800 dark:text-white">Hapus Presensi</h3>
            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
          </div>
        </div>
        <p class="text-sm text-slate-600 dark:text-slate-300 mb-5">
          Yakin ingin menghapus data kunjungan <strong>{{ deleteTarget.member?.name }}</strong> pada {{ deleteTarget.visit_time }}?
        </p>
        <div class="flex justify-end gap-3">
          <button @click="deleteTarget = null" class="btn btn-secondary">Batal</button>
          <button @click="doDelete" class="btn bg-rose-600 hover:bg-rose-700 text-white">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'
import { useNotificationStore } from '@/stores/notification'
import axios from 'axios'

const props = defineProps({
  stats: Object,
  visits: Object,
  filters: Object,
})

const filterForm = reactive({
  search: props.filters?.search || '',
  category: props.filters?.category || '',
  period: props.filters?.period || 'hari',
  perPage: props.filters?.per_page || 10,
})

function applyFilters() {
  router.get(route('visits.index'), {
    search: filterForm.search,
    category: filterForm.category,
    period: filterForm.period,
    per_page: filterForm.perPage,
  }, { preserveState: true, replace: true })
}

const barcodeInput = ref(null)
const notify = useNotificationStore()

const showScanner = ref('')
function toggleScanner(val) {
  showScanner.value = showScanner.value === val ? '' : val
}

const loading = ref(false)
const scannedMember = ref(null)

const form = useForm({
  barcode: '',
  category: '',
})

async function checkMember() {
  if (!form.barcode) {
    notify.error('Silakan scan QR Code atau masukan NIS terlebih dahulu.')
    if (barcodeInput.value) barcodeInput.value.focus()
    return
  }

  loading.value = true
  try {
    const res = await axios.post(route('visits.check'), { barcode: form.barcode })
    scannedMember.value = res.data.member
  } catch (e) {
    notify.error(e.response?.data?.message || 'Data anggota tidak ditemukan.')
    form.barcode = ''
    nextTick(() => {
      if (barcodeInput.value) barcodeInput.value.focus()
    })
  } finally {
    loading.value = false
  }
}

function submitVisit(category) {
  if (!scannedMember.value) return
  
  form.category = category
  form.post(route('visits.store'), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      form.barcode = ''
      form.category = ''
      scannedMember.value = null
      nextTick(() => {
        if (barcodeInput.value) barcodeInput.value.focus()
      })
    }
  })
}

function cancelScan() {
  form.barcode = ''
  scannedMember.value = null
  nextTick(() => {
    if (barcodeInput.value) barcodeInput.value.focus()
  })
}

// ── CRUD Methods ──────────────────────────────────────────
const isModalOpen = ref(false)
const editingVisit = ref(null)

const editForm = useForm({
  category: 'membaca',
})

function openEditModal(visit) {
  editingVisit.value = visit
  editForm.reset()
  editForm.category = visit.category
  editForm.clearErrors()
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveVisitParams() {
  editForm.put(route('visits.update', editingVisit.value.id), {
    preserveState: true,
    onSuccess: () => {
      closeModal()
    }
  })
}

// ── Delete Modal Methods ──────────────────────────────────────────
const deleteTarget = ref(null)

function confirmDelete(visit) {
  deleteTarget.value = visit
}

function doDelete() {
  if (!deleteTarget.value) return
  router.delete(route('visits.destroy', deleteTarget.value.id), {
    preserveState: true,
    onSuccess: () => {
      deleteTarget.value = null
    }
  })
}
</script>

<style scoped>
.step-badge {
  width: 28px; height: 28px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: white; font-size: 12px; font-weight: 700;
  background: #2b5a41;
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
