<template>
  <AdminLayout :title="pageTitle">

    <!-- ════════════════════════════════════════════════
         PEMINJAMAN
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'peminjaman'" class="max-w-3xl mx-auto space-y-6">

      <!-- Step 1: Scan Anggota -->
      <div class="card">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">1</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan QR Anggota</div>
          </div>
        </div>
        <div class="card-body">
          <div class="flex gap-2">
            <input v-model="pinjam.memberCode" @keyup.enter="pinjamValidateMember" type="text"
              class="form-input flex-1" placeholder="Scan QR code atau ketik kode anggota..." />
            <button @click="toggleScanner('pinjam-qr')" type="button"
              :class="['btn', showScanner === 'pinjam-qr' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'pinjam-qr' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="pinjamValidateMember" :disabled="!pinjam.memberCode || pinjam.memberLoading" class="btn btn-primary">
              <span v-if="pinjam.memberLoading" class="spinner"></span>
              <span v-else>Validasi</span>
            </button>
          </div>

          <CameraScanner type="qr" :active="showScanner === 'pinjam-qr'"
            @scanned="v => { pinjam.memberCode = v; showScanner = ''; pinjamValidateMember() }"
            @close="showScanner = ''" />

          <!-- Member Info -->
          <div v-if="pinjam.member" class="mt-4 p-4 rounded-xl flex items-center gap-4 bg-emerald-50 border border-emerald-200">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 bg-gradient-to-br from-[#2b5a41] to-[#1c3b2b]">
              {{ pinjam.member.name[0].toUpperCase() }}
            </div>
            <div class="flex-1">
              <div class="font-semibold text-slate-800">{{ pinjam.member.name }}</div>
              <div class="text-sm text-slate-500">{{ pinjam.member.member_code }} · Sisa kuota: <strong class="text-green-600">{{ pinjam.quota }} buku</strong></div>
            </div>
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div v-if="pinjam.memberError" class="mt-3 p-3 rounded-xl text-sm text-red-600 bg-red-50 border border-red-200">⚠️ {{ pinjam.memberError }}</div>
        </div>
      </div>

      <!-- Step 2: Scan Buku -->
      <div class="card" :class="!pinjam.member ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">2</div>
            <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku (maks. {{ pinjam.quota }} buku)</div>
          </div>
        </div>
        <div class="card-body space-y-4">
          <div class="flex gap-2">
            <input v-model="pinjam.barcode" @keyup.enter="pinjamAddBook" type="text"
              class="form-input flex-1" placeholder="Scan barcode atau ketik kode buku..." />
            <button @click="toggleScanner('pinjam-barcode')" type="button"
              :class="['btn', showScanner === 'pinjam-barcode' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'pinjam-barcode' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="pinjamAddBook" :disabled="!pinjam.barcode || pinjam.bookLoading" class="btn btn-primary">
              <span v-if="pinjam.bookLoading" class="spinner"></span>
              <span v-else>+ Tambah</span>
            </button>
          </div>

          <CameraScanner type="barcode" :active="showScanner === 'pinjam-barcode'"
            @scanned="v => { pinjam.barcode = v; showScanner = ''; pinjamAddBook() }"
            @close="showScanner = ''" />

          <div v-if="pinjam.books.length > 0" class="space-y-2">
            <div v-for="(bk, i) in pinjam.books" :key="i" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-indigo-50">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="#2b5a41" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div class="flex-1">
                <div class="text-sm font-semibold text-slate-800">{{ bk.book?.title }}</div>
                <div class="text-xs text-slate-400">{{ bk.copy_code }} · {{ bk.book?.author }}</div>
              </div>
              <button @click="pinjam.books.splice(i, 1)" class="text-red-400 hover:text-red-600">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>
          <div v-if="pinjam.bookError" class="p-3 rounded-xl text-sm text-red-600 bg-red-50 border border-red-200">⚠️ {{ pinjam.bookError }}</div>
        </div>
      </div>

      <!-- Step 3: Konfirmasi -->
      <div class="card" :class="pinjam.books.length === 0 ? 'opacity-50 pointer-events-none' : ''">
        <div class="card-header">
          <div class="flex items-center gap-3">
            <div class="step-badge">3</div>
            <div class="font-semibold text-slate-800 dark:text-white">Konfirmasi Peminjaman</div>
          </div>
        </div>
        <div class="card-body space-y-4">
          <div class="form-group">
            <label class="form-label">Tipe Peminjaman</label>
            <select v-model="pinjam.loanType" class="form-input">
              <option value="pembaca">Pembaca Biasa (~3 hari kerja)</option>
              <option value="lomba">Lomba / Penelitian (~30 hari)</option>
            </select>
          </div>
          <div class="p-4 rounded-xl space-y-2 bg-slate-50 border border-slate-200">
            <div class="flex justify-between text-sm"><span class="text-slate-500">Anggota</span><span class="font-semibold">{{ pinjam.member?.name || '-' }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">Jumlah Buku</span><span class="font-semibold">{{ pinjam.books.length }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">Tipe</span><span class="font-semibold capitalize">{{ pinjam.loanType }}</span></div>
          </div>
          <button @click="pinjamSubmit" :disabled="!pinjam.member || pinjam.books.length === 0 || pinjam.submitting" class="btn btn-primary w-full justify-center py-2.5">
            <span v-if="pinjam.submitting" class="spinner"></span>
            {{ pinjam.submitting ? 'Memproses...' : '✓ Konfirmasi Peminjaman' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════
         TAB 2: PENGEMBALIAN
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'pengembalian'" class="max-w-2xl mx-auto space-y-6">

      <!-- Scanner -->
      <div class="card">
        <div class="card-header">
          <div class="font-semibold text-slate-800 dark:text-white">Scan Barcode Buku</div>
        </div>
        <div class="card-body">
          <div class="flex gap-2">
            <input v-model="retur.barcode" @keyup.enter="returCheckBook" type="text"
              class="form-input flex-1 text-lg" placeholder="Scan barcode buku yang dikembalikan..." autofocus />
            <button @click="toggleScanner('retur-barcode')" type="button"
              :class="['btn', showScanner === 'retur-barcode' ? 'btn-secondary' : 'btn-outline']">
              📷 {{ showScanner === 'retur-barcode' ? 'Tutup' : 'Kamera' }}
            </button>
            <button @click="returCheckBook" :disabled="!retur.barcode || retur.loading" class="btn btn-primary px-6">
              <span v-if="retur.loading" class="spinner"></span>
              <span v-else>Cek</span>
            </button>
          </div>

          <CameraScanner type="barcode" :active="showScanner === 'retur-barcode'"
            @scanned="v => { retur.barcode = v; showScanner = ''; returCheckBook() }"
            @close="showScanner = ''" />
        </div>
      </div>

      <!-- Result -->
      <div v-if="retur.result" class="card">
        <div class="card-header"><div class="font-semibold text-slate-800 dark:text-white">Informasi Buku</div></div>
        <div class="card-body space-y-4">
          <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div><div class="text-xs text-slate-400 mb-1">Judul Buku</div><div class="font-semibold text-slate-800">{{ retur.result.copy?.book?.title }}</div></div>
            <div><div class="text-xs text-slate-400 mb-1">Kode Eksemplar</div><div class="font-mono text-sm">{{ retur.result.copy?.copy_code }}</div></div>
            <div><div class="text-xs text-slate-400 mb-1">Anggota</div><div class="font-semibold text-slate-800">{{ retur.result.member?.name }}</div></div>
            <div>
              <div class="text-xs text-slate-400 mb-1">Jatuh Tempo</div>
              <div class="font-semibold" :class="retur.result.is_overdue ? 'text-red-600' : 'text-green-600'">
                {{ formatDate(retur.result.due_date) }}
                <span v-if="retur.result.is_overdue" class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600">Terlambat!</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Kondisi Buku Setelah Dikembalikan</label>
            <div class="grid grid-cols-2 gap-3">
              <label v-for="opt in returConditions" :key="opt.value" class="cursor-pointer">
                <input v-model="retur.condition" type="radio" :value="opt.value" class="sr-only" />
                <div class="p-3 rounded-xl border-2 text-center text-sm font-medium transition-all"
                  :class="retur.condition === opt.value ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                  {{ opt.icon }} {{ opt.label }}
                </div>
              </label>
            </div>
          </div>

          <button @click="returProcess" :disabled="!retur.condition || retur.processing" class="btn btn-primary w-full justify-center py-3">
            <span v-if="retur.processing" class="spinner"></span>
            {{ retur.processing ? 'Memproses...' : '✓ Proses Pengembalian' }}
          </button>
        </div>
      </div>

      <!-- Fine Result -->
      <div v-if="retur.fineResult" class="card">
        <div class="card-body">
          <div v-if="retur.fineResult.fines?.length > 0" class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="font-semibold text-red-700 mb-2">⚠️ Denda Terdeteksi</div>
            <div v-for="fine in retur.fineResult.fines" :key="fine.id" class="flex justify-between text-sm text-red-600 py-1">
              <span>{{ fineLabel(fine.fine_type) }}</span>
              <span class="font-bold">{{ formatRupiah(fine.amount) }}</span>
            </div>
          </div>
          <div v-else class="p-4 rounded-xl text-center bg-emerald-50 border border-emerald-200">
            <div class="text-green-600 font-semibold">✓ Pengembalian berhasil, tidak ada denda!</div>
          </div>
          <button @click="returReset" class="btn btn-secondary w-full justify-center mt-4">Scan Buku Berikutnya</button>
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════
         TAB 3: RIWAYAT
         ════════════════════════════════════════════════ -->
    <div v-if="page === 'riwayat'" class="space-y-4 md:space-y-6">

      <!-- Rows per page, Filter & Search (separate card) -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Left: Rows per page -->
        <div class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
          <span>Tampilkan</span>
          <select v-model="riwayat.perPage" @change="riwayatApply"
            class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span>baris</span>
        </div>

        <!-- Right: Status filter & Search -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
          <select v-model="riwayat.status" @change="riwayatApply"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="diperpanjang">Diperpanjang</option>
            <option value="terlambat">Terlambat</option>
            <option value="selesai">Selesai</option>
          </select>

          <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input v-model="riwayat.search" @keyup.enter="riwayatApply" type="text"
              placeholder="Cari kode / nama anggota..."
              class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
          </div>
        </div>
      </div>

      <!-- Table (separate card) -->
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">No</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Kode Pinjam</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Anggota</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Buku</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Tgl Pinjam</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Jatuh Tempo</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Status</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="loans && loans.data && loans.data.length > 0" v-for="(loan, i) in loans.data" :key="loan.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900">{{ (loans.from || 1) + i }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-mono text-gray-500">{{ loan.loan_code }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600">
                    {{ loan.member?.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ loan.member?.name }}</div>
                    <div class="text-xs text-gray-400">{{ loan.member?.member_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="text-sm text-gray-900">{{ loan.items?.length }} buku</div>
                <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ loan.items?.map(it => it.copy?.book?.title).join(', ') }}</div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-900">{{ formatDate(loan.loan_date) }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="text-sm font-medium" :class="isOverdue(loan) ? 'text-red-600' : 'text-gray-900'">
                  {{ formatDate(loan.extended_due_date || loan.due_date) }}
                </span>
                <span v-if="isOverdue(loan)" class="block text-xs text-red-400 font-semibold">Terlambat!</span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-700': loan.status === 'aktif',
                    'bg-amber-100 text-amber-700': loan.status === 'diperpanjang',
                    'bg-red-100 text-red-700': loan.status === 'terlambat',
                    'bg-gray-100 text-gray-500': loan.status === 'selesai',
                  }">
                  {{ loan.status }}
                </span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex gap-2">
                  <button @click="openDetailModal(loan)" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-700 font-medium">Detail</button>
                  <button v-if="['aktif','diperpanjang'].includes(loan.status)" @click="riwayatExtend(loan)" class="px-3 py-1 text-sm text-amber-600 hover:text-amber-700 font-medium">Perpanjang</button>
                </div>
              </td>
            </tr>
            <!-- Empty state row -->
            <tr v-if="!loans || !loans.data || loans.data.length === 0">
              <td colspan="8" class="px-6 py-16 text-center">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 text-gray-300"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div class="text-gray-500 font-medium">Belum ada riwayat transaksi</div>
                <p class="text-sm text-gray-400 mt-1">Buat peminjaman pertama di menu Peminjaman</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (standalone, outside table card) -->
      <div v-if="loans && loans.data && loans.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-semibold text-gray-800">{{ loans.from || 0 }}</span> sampai <span class="font-semibold text-gray-800">{{ loans.to || 0 }}</span> dari <span class="font-semibold text-gray-800">{{ loans.total }}</span> transaksi
        </div>
        <div v-if="loans.last_page > 1" class="flex gap-1.5 items-center">
          <template v-for="(link, idx) in loans.links" :key="idx">
            <Link v-if="link.url" :href="link.url"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
              :class="link.active ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
              v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)" />
            <span v-else class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 text-sm border border-gray-200" v-html="link.label.includes('Previous') ? '‹' : (link.label.includes('Next') ? '›' : link.label)"></span>
          </template>
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════
         DETAIL MODAL
         ════════════════════════════════════════════════ -->
    <div v-if="detailModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="detailModal.show = false">
      <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-emerald-500 to-emerald-600">
              {{ detailModal.loan?.member?.name?.[0]?.toUpperCase() }}
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ detailModal.loan?.loan_code }}</h3>
              <p class="text-sm text-gray-500">{{ detailModal.loan?.member?.name }} · {{ detailModal.loan?.member?.member_code }}</p>
            </div>
          </div>
          <button @click="detailModal.show = false" class="text-gray-400 hover:text-gray-600 transition">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>

        <!-- Modal Body (scrollable) -->
        <div class="px-6 py-5 overflow-y-auto">

          <!-- Loan Meta Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Tgl Pinjam</div>
              <div class="text-sm font-semibold text-gray-900">{{ formatDate(detailModal.loan?.loan_date) }}</div>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Jatuh Tempo</div>
              <div class="text-sm font-semibold" :class="isOverdue(detailModal.loan) ? 'text-red-600' : 'text-gray-900'">
                {{ formatDate(detailModal.loan?.extended_due_date || detailModal.loan?.due_date) }}
              </div>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Status</div>
              <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                :class="{
                  'bg-green-100 text-green-700': detailModal.loan?.status === 'aktif',
                  'bg-amber-100 text-amber-700': detailModal.loan?.status === 'diperpanjang',
                  'bg-red-100 text-red-700': detailModal.loan?.status === 'terlambat',
                  'bg-gray-100 text-gray-500': detailModal.loan?.status === 'selesai',
                }">
                {{ detailModal.loan?.status }}
              </span>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
              <div class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Jumlah Buku</div>
              <div class="text-sm font-semibold text-gray-900">{{ detailModal.loan?.items?.length || 0 }} item</div>
            </div>
          </div>

          <!-- Book Items -->
          <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Detail Buku</h4>
            <div class="space-y-3">
              <div v-for="item in detailModal.loan?.items" :key="item.id" class="p-4 rounded-xl bg-white border border-gray-200 flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                  <div class="font-semibold text-gray-900 truncate">{{ item.copy?.book?.title }}</div>
                  <div class="text-sm text-gray-500 mt-0.5">{{ item.copy?.book?.author }}</div>
                  <div class="text-xs text-gray-400 mt-1 font-mono">{{ item.copy?.copy_code }} · {{ item.copy?.barcode }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                  <div v-if="item.returned_at">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Dikembalikan</span>
                    <div class="text-xs text-gray-400 mt-1">{{ formatDate(item.returned_at) }}</div>
                    <div class="text-xs mt-0.5" :class="item.condition_after === 'baik' ? 'text-green-600' : 'text-red-500'">
                      Kondisi: {{ item.condition_after }}
                    </div>
                  </div>
                  <span v-else class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Belum Dikembalikan</span>
                  <!-- Fines -->
                  <div v-if="item.fines?.length" class="mt-2 space-y-1">
                    <div v-for="fine in item.fines" :key="fine.id" class="text-xs">
                      <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Denda {{ fineLabel(fine.fine_type) }}: Rp {{ Number(fine.amount).toLocaleString('id-ID') }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Extension History -->
          <div v-if="detailModal.loan?.extensions?.length">
            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Riwayat Perpanjangan</h4>
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl">
              <table class="w-full">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">#</th>
                    <th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Jatuh Tempo Lama</th>
                    <th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Jatuh Tempo Baru</th>
                    <th class="px-4 py-2 text-xs font-bold text-gray-600 text-left uppercase">Oleh</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="(ext, i) in detailModal.loan.extensions" :key="ext.id">
                    <td class="px-4 py-2 text-sm text-gray-500">{{ i + 1 }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(ext.old_due_date) }}</td>
                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ formatDate(ext.new_due_date) }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ ext.extended_by?.name }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
          <div class="flex gap-2">
            <button v-if="['aktif','diperpanjang'].includes(detailModal.loan?.status)" @click="riwayatExtend(detailModal.loan); detailModal.show = false" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg text-sm transition">Perpanjang</button>
          </div>
          <button @click="detailModal.show = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">Tutup</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import CameraScanner from '@/Components/CameraScanner.vue'

const props = defineProps({ page: String, loans: Object, filters: Object })

const pageTitle = computed(() => ({
  peminjaman: 'Peminjaman',
  pengembalian: 'Pengembalian',
  riwayat: 'Riwayat Transaksi',
})[props.page] || 'Sirkulasi')

const showScanner = ref('')
function toggleScanner(id) {
  showScanner.value = showScanner.value === id ? '' : id
}

// ── TAB 1: PEMINJAMAN ──
const pinjam = reactive({
  memberCode: '', barcode: '', member: null, quota: 2,
  memberError: '', memberLoading: false,
  bookLoading: false, bookError: '', books: [],
  loanType: 'pembaca', submitting: false,
})

async function pinjamValidateMember() {
  if (!pinjam.memberCode) return
  pinjam.memberLoading = true; pinjam.memberError = ''; pinjam.member = null
  try {
    const res = await axios.post(route('loans.validate-member'), { member_code: pinjam.memberCode })
    if (res.data.valid) { pinjam.member = res.data.member; pinjam.quota = res.data.quota ?? 2 }
    else { pinjam.memberError = res.data.message }
  } catch (e) { pinjam.memberError = e.response?.data?.message || 'Terjadi kesalahan.' }
  finally { pinjam.memberLoading = false }
}

async function pinjamAddBook() {
  if (!pinjam.barcode || pinjam.books.length >= pinjam.quota) return
  pinjam.bookLoading = true; pinjam.bookError = ''
  try {
    const res = await axios.post(route('loans.validate-book'), { barcode: pinjam.barcode })
    if (res.data.valid) {
      if (pinjam.books.find(b => b.barcode === res.data.copy.barcode)) { pinjam.bookError = 'Buku ini sudah ditambahkan.' }
      else { pinjam.books.push(res.data.copy) }
    } else { pinjam.bookError = res.data.message }
    pinjam.barcode = ''
  } catch (e) { pinjam.bookError = e.response?.data?.message || 'Buku tidak ditemukan.' }
  finally { pinjam.bookLoading = false }
}

function pinjamSubmit() {
  pinjam.submitting = true
  router.post(route('loans.store'), {
    member_code: pinjam.memberCode, barcodes: pinjam.books.map(b => b.barcode), loan_type: pinjam.loanType,
  }, { onFinish: () => { pinjam.submitting = false } })
}

// ── TAB 2: PENGEMBALIAN ──
const retur = reactive({
  barcode: '', result: null, fineResult: null,
  condition: '', loading: false, processing: false,
})

const returConditions = [
  { value: 'baik',         icon: '✅', label: 'Baik' },
  { value: 'rusak_ringan', icon: '⚠️', label: 'Rusak Ringan' },
  { value: 'rusak_berat',  icon: '🔴', label: 'Rusak Berat' },
  { value: 'hilang',       icon: '❌', label: 'Hilang' },
]

async function returCheckBook() {
  if (!retur.barcode) return
  retur.loading = true; retur.result = null; retur.fineResult = null; retur.condition = ''
  try {
    const res = await axios.post(route('returns.check'), { barcode: retur.barcode })
    if (res.data.on_loan) { retur.result = res.data }
    else { alert(res.data.message) }
  } catch (e) { alert(e.response?.data?.message || 'Buku tidak ditemukan.') }
  finally { retur.loading = false }
}

async function returProcess() {
  retur.processing = true
  try {
    const res = await axios.post(route('returns.store'), { barcode: retur.barcode, condition_after: retur.condition })
    retur.fineResult = res.data; retur.result = null
  } catch (e) { alert(e.response?.data?.message || 'Gagal memproses pengembalian.') }
  finally { retur.processing = false }
}

function returReset() { retur.barcode = ''; retur.result = null; retur.fineResult = null; retur.condition = '' }

// ── TAB 3: RIWAYAT ──
const detailModal = reactive({ show: false, loan: null })
function openDetailModal(loan) { detailModal.loan = loan; detailModal.show = true }
function fineLabel(t) { return { keterlambatan: 'Keterlambatan', kerusakan: 'Kerusakan', kehilangan: 'Kehilangan' }[t] ?? t }

const riwayat = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  perPage: props.filters?.per_page || 10,
})

function riwayatApply() {
  router.get(route('sirkulasi.riwayat'), {
    search: riwayat.search,
    status: riwayat.status,
    per_page: riwayat.perPage,
  }, { preserveState: true, replace: true })
}

function riwayatExtend(loan) {
  if (!confirm(`Perpanjang pinjaman ${loan.loan_code}?`)) return
  router.post(route('loans.extend', loan.id))
}

// ── Helpers ──
function formatDate(d) { return d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }
function formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') }
function isOverdue(loan) {
  if (!['aktif', 'diperpanjang'].includes(loan.status)) return false
  return new Date(loan.extended_due_date || loan.due_date) < new Date()
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
