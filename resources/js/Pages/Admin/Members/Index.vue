<template>
  <AdminLayout title="Data Anggota">
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <input v-model="filters.search" @keyup.enter="applyFilter" type="text" placeholder="Cari nama / kode..." class="form-input py-1.5 w-56" />
        <select v-model="filters.status" @change="applyFilter" class="form-input py-1.5 w-40">
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="aktif">Aktif</option>
          <option value="suspended">Suspended</option>
          <option value="nonaktif">Nonaktif</option>
          <option value="ditolak">Ditolak</option>
        </select>
        <select v-model="filters.type" @change="applyFilter" class="form-input py-1.5 w-36">
          <option value="">Semua Tipe</option>
          <option value="siswa">Siswa</option>
          <option value="guru">Guru</option>
          <option value="umum">Umum</option>
        </select>
        <Link :href="route('members.create')" class="btn py-1.5 btn-primary gap-1">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Tambah Manual
        </Link>
        <button @click="showImportModal = true" class="btn py-1.5 btn-success gap-1">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Impor Excel
        </button>
      </div>
    </template>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="font-semibold text-slate-800">Daftar Anggota</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ members.total }} anggota ditemukan</div>
        </div>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Anggota</th>
              <th>Kode</th>
              <th>Tipe</th>
              <th>Kelas</th>
              <th>Status</th>
              <th>Bergabung</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="m in members.data" :key="m.id">
              <td>
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    {{ m.name[0].toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-medium text-slate-800">{{ m.name }}</div>
                    <div class="text-xs text-slate-400">{{ m.user?.email }}</div>
                  </div>
                </div>
              </td>
              <td class="font-mono text-xs text-slate-500">{{ m.member_code }}</td>
              <td><span class="badge" :class="typeClass(m.type)">{{ m.type }}</span></td>
              <td class="text-sm text-slate-600">{{ m.kelas?.name || '-' }}</td>
              <td><MemberStatusBadge :status="m.status" /></td>
              <td class="text-sm text-slate-400">{{ formatDate(m.created_at) }}</td>
              <td>
                <div class="flex gap-2">
                  <button @click="openDetailModal(m)" class="btn btn-sm btn-secondary">Detail</button>
                  <button v-if="m.status === 'pending'" @click="approve(m)" class="btn btn-sm btn-success">✓</button>
                </div>
              </td>
            </tr>
            <tr v-if="members.data.length === 0">
              <td colspan="7">
                <div class="empty-state">
                  <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3"><path d="M15 19.128a9.38 9.38 0 0 0 2.625.372..." stroke="#94a3b8" stroke-width="1.4"/></svg>
                  <div>Tidak ada anggota ditemukan</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="members.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-sm text-slate-500">Halaman {{ members.current_page }} dari {{ members.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="members.prev_page_url" :href="members.prev_page_url" class="btn btn-sm btn-secondary">← Prev</Link>
          <Link v-if="members.next_page_url" :href="members.next_page_url" class="btn btn-sm btn-secondary">Next →</Link>
        </div>
      </div>
    </div>

    <!-- Modal Import Excel -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-6">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-800">Impor Data Anggota</h2>
            <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600">
              <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
          <form @submit.prevent="submitImport" class="space-y-4">
            <div class="space-y-1.5">
              <label class="form-label">Tipe Anggota</label>
              <select v-model="importForm.type" class="form-input" required>
                <option value="siswa">Siswa</option>
                <option value="guru">Guru / Karyawan</option>
              </select>
            </div>
            <div class="space-y-1.5">
              <label class="form-label">File Excel</label>
              <input type="file" @change="e => importForm.file = e.target.files[0]" accept=".xlsx,.xls,.csv" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required />
              <p v-if="importForm.errors.file" class="text-xs text-red-500 mt-1">{{ importForm.errors.file }}</p>
            </div>
            <div class="pt-2 flex justify-between items-center">
              <a :href="route('members.template', importForm.type)" class="text-xs font-semibold text-primary-600 hover:text-primary-700">
                ↓ Download Template
              </a>
              <div class="flex gap-2">
                <button type="button" @click="showImportModal = false" class="btn btn-secondary">Batal</button>
                <button type="submit" :disabled="importForm.processing" class="btn btn-primary min-w-[100px] justify-center">
                  <span v-if="importForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                  <span v-else>Impor</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Detail Anggota -->
    <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
              {{ selectedMember?.name[0].toUpperCase() }}
            </div>
            <div>
              <h2 class="text-lg font-bold text-slate-800">{{ selectedMember?.name }}</h2>
              <p class="text-sm text-slate-500 font-mono">{{ selectedMember?.member_code }}</p>
            </div>
          </div>
          <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <div class="p-6 overflow-y-auto">
          <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Basic Status -->
            <div class="col-span-2 lg:col-span-3 flex gap-3 mb-2">
              <span class="badge" :class="typeClass(selectedMember?.type)">{{ selectedMember?.type.toUpperCase() }}</span>
              <MemberStatusBadge :status="selectedMember?.status" />
            </div>

            <!-- Email & Kontak -->
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Akun Login</div>
              <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.user?.email || 'Belum ada akun' }}</div>
            </div>
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">No. Handphone</div>
              <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.phone || '-' }}</div>
            </div>
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Tgl Bergabung</div>
              <div class="text-sm text-slate-700 font-medium">{{ selectedMember ? formatDate(selectedMember.created_at) : '-' }}</div>
            </div>

            <!-- Spesifik Tipe: Siswa -->
            <template v-if="selectedMember?.type === 'siswa'">
              <div>
                <div class="text-xs uppercase text-slate-400 font-semibold mb-1">NIS</div>
                <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.nis_nip || '-' }}</div>
              </div>
              <div>
                <div class="text-xs uppercase text-slate-400 font-semibold mb-1">NISN</div>
                <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.nisn || '-' }}</div>
              </div>
              <div>
                <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Kelas</div>
                <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.kelas?.name || '-' }}</div>
              </div>
            </template>

            <!-- Spesifik Tipe: Guru -->
            <template v-if="selectedMember?.type === 'guru'">
              <div>
                <div class="text-xs uppercase text-slate-400 font-semibold mb-1">NIP</div>
                <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.nis_nip || '-' }}</div>
              </div>
              <div class="col-span-2">
                <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Pangkat / Golongan</div>
                <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.pangkat_golongan || '-' }}</div>
              </div>
            </template>

            <!-- Data Pribadi -->
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">NIK</div>
              <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.nik || '-' }}</div>
            </div>
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">TTL</div>
              <div class="text-sm text-slate-700 font-medium">
                {{ selectedMember?.tempat_lahir || '-' }}, 
                {{ selectedMember?.tanggal_lahir ? formatDate(selectedMember.tanggal_lahir) : '-' }}
              </div>
            </div>
            <div>
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Jenis Kelamin</div>
              <div class="text-sm text-slate-700 font-medium">{{ selectedMember?.jenis_kelamin === 'L' ? 'Laki-laki' : (selectedMember?.jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
            </div>
            
            <div class="col-span-2 lg:col-span-3">
              <div class="text-xs uppercase text-slate-400 font-semibold mb-1">Alamat</div>
              <div class="text-sm text-slate-700 font-medium leading-relaxed">{{ selectedMember?.address || '-' }}</div>
            </div>
          </div>
          
          <!-- Hapus Tombol Ke Profil Lengkap untuk Sementara -->
          <div class="mt-8 flex justify-end">
            <Link :href="route('members.show', selectedMember?.id)" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
              Lihat Histori Sirkulasi & Update Data &rarr;
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import MemberStatusBadge from '@/Components/MemberStatusBadge.vue'

const props = defineProps({ members: Object, filters: Object })

const showDetailModal = ref(false)
const selectedMember = ref(null)

function openDetailModal(member) {
  selectedMember.value = member
  showDetailModal.value = true
}

const filters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  type:   props.filters?.type   || '',
})

const showImportModal = ref(false)
const importForm = useForm({
  type: 'siswa',
  file: null,
})

function applyFilter() {
  router.get(route('members.index'), filters, { preserveState: true, replace: true })
}

function submitImport() {
  importForm.post(route('members.import'), {
    preserveScroll: true,
    onSuccess: () => {
      showImportModal.value = false
      importForm.reset()
    },
  })
}

function approve(member) {
  if (confirm(`Setujui anggota ${member.name}?`)) {
    router.post(route('members.approve', member.id))
  }
}

function typeClass(type) {
  return { siswa: 'badge-blue', guru: 'badge-purple', umum: 'badge-gray' }[type] ?? 'badge-gray'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>
