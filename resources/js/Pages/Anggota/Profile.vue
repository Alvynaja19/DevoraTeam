<template>
  <PublicLayout>
    <div class="profile-page">
      <div class="profile-inner">

        <!-- ─── Header Card ──────────────────────────────── -->
        <div class="profile-header-card">
          <!-- Avatar / Photo -->
          <div class="profile-avatar-wrap">
            <img v-if="member?.photo" :src="`/storage/${member.photo}`"
              alt="Foto profil" class="profile-photo" />
            <div v-else class="profile-avatar">
              {{ user.name[0].toUpperCase() }}
            </div>
          </div>

          <div class="profile-identity">
            <h1 class="profile-name">{{ user.name }}</h1>
            <div class="profile-email">{{ user.email }}</div>
            <div class="profile-badges">
              <span class="badge-type">{{ member?.type || 'Anggota' }}</span>
              <span class="badge-status" :class="statusClass(member?.status)">
                {{ statusLabel(member?.status) }}
              </span>
              <span v-if="member?.kelas" class="badge-kelas">{{ member.kelas.name }}</span>
            </div>
          </div>

          <div class="profile-code-block">
            <div class="code-label">Kode Anggota</div>
            <div class="code-value">{{ member?.member_code || '—' }}</div>
          </div>
        </div>

        <!-- ─── Flash success ─────────────────────────── -->
        <div v-if="$page.props.flash?.success"
          class="flash-success">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
          </svg>
          {{ $page.props.flash.success }}
        </div>

        <!-- ─── Tabs ─────────────────────────────────── -->
        <div class="profile-tabs">
          <button @click="activeTab = 'info'" :class="['tab-btn', activeTab === 'info' && 'active']">
            Informasi Akun
          </button>
          <button @click="activeTab = 'qr'" :class="['tab-btn', activeTab === 'qr' && 'active']">
            📱 QR Saya
          </button>
          <button @click="activeTab = 'edit'" :class="['tab-btn', activeTab === 'edit' && 'active']">
            ✏️ Edit Profil
          </button>
          <button @click="activeTab = 'loans'" :class="['tab-btn', activeTab === 'loans' && 'active']">
            Peminjaman Aktif
            <span v-if="activeLoans.length" class="tab-badge">{{ activeLoans.length }}</span>
          </button>
        </div>

        <!-- ─── Tab: Info ─────────────────────────────── -->
        <div v-if="activeTab === 'info'" class="profile-section">
          <div class="info-list">
            <div class="info-row">
              <span class="info-label">Nama Lengkap</span>
              <span class="info-value">{{ user.name }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email</span>
              <span class="info-value">{{ user.email }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Tipe Anggota</span>
              <span class="info-value capitalize">{{ member?.type || '—' }}</span>
            </div>
            <div v-if="member?.kelas" class="info-row">
              <span class="info-label">Kelas</span>
              <span class="info-value">{{ member.kelas.name }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">No. HP</span>
              <span class="info-value">
                {{ member?.phone || '—' }}
                <button v-if="!member?.phone" @click="activeTab = 'edit'"
                  class="fill-prompt">+ Isi sekarang</button>
              </span>
            </div>
            <div class="info-row">
              <span class="info-label">NIS / NIP</span>
              <span class="info-value">
                {{ member?.nis_nip || '—' }}
                <button v-if="!member?.nis_nip" @click="activeTab = 'edit'"
                  class="fill-prompt">+ Isi sekarang</button>
              </span>
            </div>
            <div class="info-row">
              <span class="info-label">Alamat</span>
              <span class="info-value">
                {{ member?.address || '—' }}
                <button v-if="!member?.address" @click="activeTab = 'edit'"
                  class="fill-prompt">+ Isi sekarang</button>
              </span>
            </div>
            <div class="info-row">
              <span class="info-label">Bergabung</span>
              <span class="info-value">{{ formatDate(member?.created_at) }}</span>
            </div>
            <div v-if="member?.verified_at" class="info-row">
              <span class="info-label">Diverifikasi</span>
              <span class="info-value">{{ formatDate(member.verified_at) }}</span>
            </div>
          </div>

          <!-- Completeness bar -->
          <div class="completeness">
            <div class="completeness-header">
              <span>Kelengkapan Profil</span>
              <span class="completeness-pct">{{ completeness }}%</span>
            </div>
            <div class="completeness-bar">
              <div class="completeness-fill" :style="{ width: completeness + '%' }"
                :class="completeness >= 80 ? 'full' : completeness >= 50 ? 'mid' : 'low'"/>
            </div>
            <p v-if="completeness < 100" class="completeness-hint">
              Lengkapi profil kamu agar data keanggotaan lebih lengkap.
              <button @click="activeTab = 'edit'" class="fill-prompt">Lengkapi →</button>
            </p>
          </div>
        </div>

        <!-- ─── Tab: QR Saya ────────────────────────────── -->
        <div v-if="activeTab === 'qr'" class="profile-section qr-section">
          <div v-if="member?.member_code" class="qr-container">
            <div class="qr-card">
              <canvas ref="qrCanvas" class="qr-canvas"></canvas>
            </div>
            <div class="qr-info">
              <div class="qr-name">{{ user.name }}</div>
              <div class="qr-code">{{ member.member_code }}</div>
              <div class="qr-ready">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                </svg>
                QR siap digunakan — tunjukkan ke petugas
              </div>
            </div>
            <p class="qr-desc">
              Tunjukkan QR ini kepada petugas perpustakaan untuk proses peminjaman buku.
            </p>
          </div>
          <div v-else class="qr-empty">
            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
              <path d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"
                stroke="#98a2b3" stroke-width="1.4"/>
            </svg>
            <p class="text-sm text-gray-500">QR belum tersedia</p>
          </div>
        </div>

        <!-- ─── Tab: Edit Profil ───────────────────────── -->
        <div v-if="activeTab === 'edit'" class="profile-section">
          <form @submit.prevent="submitEdit" enctype="multipart/form-data" class="edit-form">

            <!-- Foto Profil -->
            <div class="form-group">
              <label class="form-label">Foto Profil</label>
              <div class="photo-upload-area">
                <div class="photo-preview">
                  <img v-if="photoPreview" :src="photoPreview" alt="Preview" class="preview-img" />
                  <img v-else-if="member?.photo" :src="`/storage/${member.photo}`" alt="Foto" class="preview-img" />
                  <div v-else class="preview-placeholder">
                    {{ user.name[0].toUpperCase() }}
                  </div>
                </div>
                <div class="photo-upload-info">
                  <label class="photo-upload-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                      <path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pilih Foto
                    <input type="file" accept="image/*" @change="onPhotoChange" class="hidden" />
                  </label>
                  <p class="photo-hint">JPG, PNG, max 2MB</p>
                </div>
              </div>
              <p v-if="form.errors?.photo" class="form-error">{{ form.errors.photo }}</p>
            </div>

            <div class="form-divider" />

            <!-- No. HP -->
            <div class="form-group">
              <label class="form-label">Nomor HP</label>
              <input v-model="form.phone" type="tel" placeholder="08xxxxxxxxxx"
                class="form-input" />
              <p v-if="form.errors?.phone" class="form-error">{{ form.errors.phone }}</p>
            </div>

            <!-- NIS / NIP -->
            <div class="form-group">
              <label class="form-label">
                NIS / NIP
                <span class="label-hint">(Nomor Induk Siswa / Pegawai)</span>
              </label>
              <input v-model="form.nis_nip" type="text" placeholder="Isi NIS untuk siswa atau NIP untuk guru"
                class="form-input" />
              <p v-if="form.errors?.nis_nip" class="form-error">{{ form.errors.nis_nip }}</p>
            </div>

            <!-- Alamat -->
            <div class="form-group">
              <label class="form-label">Alamat</label>
              <textarea v-model="form.address" placeholder="Jl. Contoh No. 1, Kota..."
                rows="3" class="form-input form-textarea" />
              <p v-if="form.errors?.address" class="form-error">{{ form.errors.address }}</p>
            </div>

            <!-- Submit -->
            <div class="form-actions">
              <button type="button" @click="activeTab = 'info'" class="btn-cancel">
                Batal
              </button>
              <button type="submit" class="btn-save" :disabled="form.processing">
                <svg v-if="form.processing" class="spin" width="14" height="14" fill="none" viewBox="0 0 24 24">
                  <path d="M12 3v3m0 12v3M3 12h3m12 0h3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>

        <!-- ─── Tab: Peminjaman Aktif ──────────────────── -->
        <div v-if="activeTab === 'loans'" class="profile-section">
          <div v-if="activeLoans.length > 0" class="loan-list">
            <div v-for="loan in activeLoans" :key="loan.id" class="loan-card">
              <div class="loan-meta">
                <span class="loan-code">{{ loan.loan_code }}</span>
                <span class="loan-due" :class="isOverdue(loan) ? 'overdue' : 'ok'">
                  {{ isOverdue(loan) ? '⚠ Terlambat' : 'Jatuh tempo' }}: {{ formatDate(loan.due_date) }}
                </span>
              </div>
              <div class="loan-books">
                <div v-for="item in loan.items" :key="item.id" class="loan-book-row">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" style="color:#1a6b5a;flex-shrink:0">
                    <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span>{{ item.book_copy?.book?.title || '—' }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="loan-empty">
            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="margin:0 auto 12px;opacity:0.3">
              <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                stroke="#667085" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p>Tidak ada peminjaman aktif</p>
            <Link :href="route('catalog')" class="browse-link">Jelajahi Katalog →</Link>
          </div>
        </div>

        <!-- ─── Bottom Action ─────────────────────────── -->
        <div class="profile-bottom">
          <Link :href="route('catalog')" class="btn-catalog">Jelajahi Katalog</Link>
          <Link :href="route('logout')" method="post" as="button" class="btn-logout">Keluar</Link>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import QRCode from 'qrcode'

const props = defineProps({ member: Object })
const user  = usePage().props.auth.user

const activeTab    = ref('info')
const photoPreview = ref(null)
const qrCanvas     = ref(null)

// Generate QR saat tab QR dibuka
watch(activeTab, async (tab) => {
  if (tab === 'qr' && props.member?.member_code) {
    await nextTick()
    if (qrCanvas.value) {
      await QRCode.toCanvas(qrCanvas.value, props.member.member_code, {
        width: 200,
        margin: 2,
        color: { dark: '#101828', light: '#ffffff' },
      })
    }
  }
})

// Inertia form
const form = useForm({
  phone:   props.member?.phone   || '',
  nis_nip: props.member?.nis_nip || '',
  address: props.member?.address || '',
  photo:   null,
  _method: 'PUT',
})

function onPhotoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  form.photo = file
  const reader = new FileReader()
  reader.onload = ev => { photoPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function submitEdit() {
  form.post(route('anggota.profile.update'), {
    forceFormData: true,
    onSuccess: () => {
      activeTab.value = 'info'
      photoPreview.value = null
    },
  })
}

// Computed
const activeLoans = computed(() => props.member?.loans || [])

const completeness = computed(() => {
  const fields = [user.name, user.email, props.member?.phone, props.member?.nis_nip, props.member?.address, props.member?.photo]
  const filled  = fields.filter(Boolean).length
  return Math.round((filled / fields.length) * 100)
})

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
}

function isOverdue(loan) {
  return new Date(loan.due_date) < new Date()
}

function statusLabel(s) {
  return { aktif: 'Aktif', pending: 'Menunggu Verifikasi', suspended: 'Suspended', nonaktif: 'Nonaktif', ditolak: 'Ditolak' }[s] ?? s
}

function statusClass(s) {
  return { aktif: 'status-aktif', pending: 'status-pending', suspended: 'status-suspended', nonaktif: 'status-nonaktif', ditolak: 'status-ditolak' }[s] ?? ''
}
</script>

<style scoped>
.profile-page {
  padding: 40px 24px 64px;
  min-height: calc(100vh - 64px);
  background: #faf8f3;
  font-family: 'Outfit', 'DM Sans', sans-serif;
}

.profile-inner {
  max-width: 720px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ─── Header Card ─────────────────────────────── */
.profile-header-card {
  background: linear-gradient(135deg, #1f7a4a, #2d9e64);
  border-radius: 16px;
  padding: 28px;
  display: flex;
  align-items: flex-start;
  gap: 20px;
  border: 1px solid rgba(255,255,255,0.15);
}

.profile-avatar-wrap { flex-shrink: 0; }

.profile-photo,
.profile-avatar {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  border: 3px solid rgba(255,255,255,0.12);
}

.profile-photo { object-fit: cover; }

.profile-avatar {
  background: linear-gradient(135deg, #2d9e64, #4aba82);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  font-weight: 800;
  color: white;
}

.profile-identity { flex: 1; }

.profile-name {
  font-size: 20px;
  font-weight: 800;
  color: white;
  margin-bottom: 3px;
}

.profile-email { font-size: 13px; color: #94a3b8; margin-bottom: 10px; }

.profile-badges { display: flex; flex-wrap: wrap; gap: 6px; }

.badge-type, .badge-status, .badge-kelas {
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.badge-type  { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #e2e8f0; text-transform: capitalize; }
.badge-kelas { background: rgba(37,160,126,0.2); border: 1px solid rgba(37,160,126,0.3); color: #6ee7b7; }

.status-aktif     { background: rgba(16,185,129,0.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.3); }
.status-pending   { background: rgba(245,158,11,0.2); color: #fcd34d; border: 1px solid rgba(245,158,11,0.3); }
.status-suspended { background: rgba(239,68,68,0.2);  color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
.status-nonaktif  { background: rgba(100,116,139,0.2);color: #94a3b8; border: 1px solid rgba(100,116,139,0.3);}
.status-ditolak   { background: rgba(239,68,68,0.2);  color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }

.profile-code-block { text-align: right; flex-shrink: 0; }
.code-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; margin-bottom: 3px; }
.code-value { font-size: 17px; font-weight: 800; color: white; font-family: monospace; }

.pending-info {
  display: flex; align-items: center; gap: 5px;
  margin-top: 8px; font-size: 11px; color: #fcd34d;
  background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.2);
  border-radius: 6px; padding: 5px 8px;
}

/* ─── Flash ──────────────────────────────────── */
.flash-success {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 16px; border-radius: 10px;
  background: #f0fdf4; color: #15803d;
  border: 1px solid #bbf7d0; font-size: 14px; font-weight: 500;
}

/* ─── Tabs ───────────────────────────────────── */
.profile-tabs {
  display: flex; gap: 4px;
  background: #fff; border: 1px solid #d8f3e3;
  border-radius: 12px; padding: 4px;
}

.tab-btn {
  flex: 1; padding: 8px 12px; border-radius: 8px;
  border: none; background: transparent;
  font-size: 13px; font-weight: 500; color: #667085;
  cursor: pointer; transition: all 0.15s; font-family: 'Outfit', sans-serif;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}

.tab-btn:hover { background: #f2f4f7; color: #344054; }
.tab-btn.active { background: #2d9e64; color: white; font-weight: 600; }

.tab-badge {
  background: rgba(255,255,255,0.25); color: inherit;
  font-size: 11px; font-weight: 700;
  padding: 1px 6px; border-radius: 999px;
}

.tab-btn:not(.active) .tab-badge {
  background: #d1fae5; color: #065f46;
}

/* ─── Section ────────────────────────────────── */
.profile-section {
  background: #fff; border: 1px solid #d8f3e3;
  border-radius: 16px; padding: 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}

/* Info List */
.info-list { display: flex; flex-direction: column; }

.info-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 11px 0; border-bottom: 1px solid #f2f4f7;
  font-size: 14px; gap: 12px;
}

.info-row:last-child { border-bottom: none; }
.info-label { color: #667085; font-size: 13px; flex-shrink: 0; }
.info-value { color: #101828; font-weight: 500; text-align: right; display: flex; align-items: center; gap: 8px; }

.fill-prompt {
  font-size: 12px; font-weight: 600; color: #2d9e64;
  background: none; border: none; cursor: pointer; font-family: 'Outfit', sans-serif;
  text-decoration: underline; padding: 0;
}

/* Completeness */
.completeness {
  margin-top: 20px; padding-top: 16px;
  border-top: 1px solid #f2f4f7;
}

.completeness-header {
  display: flex; justify-content: space-between;
  font-size: 13px; font-weight: 600; color: #344054;
  margin-bottom: 8px;
}

.completeness-pct { color: #2d9e64; }

.completeness-bar {
  height: 8px; border-radius: 999px; background: #f2f4f7; overflow: hidden;
}

.completeness-fill {
  height: 100%; border-radius: 999px;
  transition: width 0.4s ease;
}

.completeness-fill.full { background: #10b981; }
.completeness-fill.mid  { background: #f59e0b; }
.completeness-fill.low  { background: #ef4444; }

.completeness-hint {
  font-size: 12px; color: #667085; margin-top: 8px;
}

/* ─── Edit Form ─────────────────────────────── */
.edit-form { display: flex; flex-direction: column; gap: 20px; }

.form-group { display: flex; flex-direction: column; gap: 6px; }

.form-label {
  font-size: 14px; font-weight: 600; color: #344054;
  display: flex; align-items: center; gap: 6px;
}

.label-hint { font-size: 12px; color: #98a2b3; font-weight: 400; }

.form-input {
  width: 100%; padding: 10px 12px;
  border: 1.5px solid #d0d5dd; border-radius: 8px;
  font-size: 14px; color: #101828; background: white;
  outline: none; font-family: 'Outfit', sans-serif;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.form-input:focus {
  border-color: #2d9e64;
  box-shadow: 0 0 0 3px rgba(45, 158, 100, 0.12);
}

.form-input::placeholder { color: #98a2b3; }

.form-textarea { resize: vertical; min-height: 80px; }

.form-error { font-size: 12px; color: #dc2626; }
.form-divider { height: 1px; background: #f2f4f7; }

/* Photo Upload */
.photo-upload-area {
  display: flex; align-items: center; gap: 16px;
}

.photo-preview { flex-shrink: 0; }

.preview-img {
  width: 64px; height: 64px; border-radius: 50%;
  object-fit: cover; border: 2px solid #e4e7ec;
}

.preview-placeholder {
  width: 64px; height: 64px; border-radius: 50%;
  background: linear-gradient(135deg, #2d9e64, #4aba82);
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; font-weight: 800; color: white;
}

.photo-upload-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 8px;
  border: 1.5px solid #d0d5dd; background: white;
  font-size: 13px; font-weight: 600; color: #344054;
  cursor: pointer; transition: all 0.15s; font-family: 'Outfit', sans-serif;
}

.photo-upload-btn:hover { background: #f9fafb; }
.photo-hint { font-size: 12px; color: #98a2b3; margin-top: 4px; }
.hidden { display: none; }

/* Form Actions */
.form-actions {
  display: flex; justify-content: flex-end; gap: 10px;
  padding-top: 4px;
}

.btn-cancel {
  padding: 10px 20px; border-radius: 8px;
  border: 1.5px solid #d0d5dd; background: white;
  font-size: 14px; font-weight: 500; color: #344054;
  cursor: pointer; font-family: 'Outfit', sans-serif;
  transition: all 0.15s;
}

.btn-cancel:hover { background: #f9fafb; }

.btn-save {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 24px; border-radius: 8px; border: none;
  background: #2d9e64; color: white;
  font-size: 14px; font-weight: 600;
  cursor: pointer; font-family: 'Outfit', sans-serif;
  transition: background 0.15s;
}

.btn-save:hover:not(:disabled) { background: #1f7a4a; }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

.spin { animation: spin 0.8s linear infinite; }

@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Loans ─────────────────────────────────── */
.loan-list { display: flex; flex-direction: column; gap: 12px; }

.loan-card { border: 1.5px solid #e4e7ec; border-radius: 10px; padding: 14px; }

.loan-meta {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 10px; flex-wrap: wrap; gap: 4px;
}

.loan-code {
  font-size: 11px; font-weight: 700; font-family: monospace;
  color: #475467; background: #f2f4f7; padding: 2px 7px; border-radius: 4px;
}

.loan-due { font-size: 12px; font-weight: 600; }
.loan-due.overdue { color: #dc2626; }
.loan-due.ok      { color: #059669; }

.loan-books { display: flex; flex-direction: column; gap: 6px; }
.loan-book-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #344054; }

.loan-empty { text-align: center; padding: 40px 0; color: #94a3b8; font-size: 14px; }

.browse-link {
  display: inline-block; margin-top: 10px;
  font-size: 13px; font-weight: 600; color: #2d9e64; text-decoration: none;
}

.browse-link:hover { color: #1f7a4a; }

/* ─── QR Section ────────────────────────────── */
.qr-section { display: flex; flex-direction: column; align-items: center; }

.qr-container {
  display: flex; flex-direction: column; align-items: center; gap: 20px;
  width: 100%;
}

.qr-card {
  background: white;
  border: 2px solid #e4e7ec;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  display: inline-flex;
}

.qr-canvas { display: block; border-radius: 8px; }

.qr-info { text-align: center; }

.qr-name {
  font-size: 18px; font-weight: 800; color: #101828; margin-bottom: 4px;
}

.qr-code {
  font-size: 13px; font-family: monospace; font-weight: 600;
  color: #667085; background: #f2f4f7;
  padding: 3px 10px; border-radius: 6px; display: inline-block;
  margin-bottom: 10px;
}

.qr-warning {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 600; color: #92400e;
  background: #fef9c3; border: 1px solid #fde68a;
  padding: 6px 12px; border-radius: 8px;
}

.qr-ready {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 600; color: #065f46;
  background: #d1fae5; border: 1px solid #a7f3d0;
  padding: 6px 12px; border-radius: 8px;
}

.qr-desc {
  font-size: 13px; color: #667085; text-align: center;
  max-width: 320px;
}

.qr-empty { text-align: center; padding: 40px 0; color: #98a2b3; }

/* ─── Bottom ─────────────────────────────────── */
.profile-bottom { display: flex; justify-content: flex-end; gap: 10px; }

.btn-catalog {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 8px;
  font-size: 13px; font-weight: 600; color: white;
  text-decoration: none; background: #2d9e64;
  transition: background 0.15s;
}

.btn-catalog:hover { background: #1f7a4a; }

.btn-logout {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 8px;
  font-size: 13px; font-weight: 600; color: #dc2626;
  background: white; border: 1.5px solid #fecaca;
  cursor: pointer; transition: all 0.15s; font-family: 'Outfit', sans-serif;
}

.btn-logout:hover { background: #fef2f2; }

.capitalize { text-transform: capitalize; }
</style>
