<template>
  <AdminLayout title="Katalog Buku">
    <!-- Topbar actions -->
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <button @click="isImportModalOpen = true" class="btn btn-secondary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
          Import Excel
        </button>
        <Link :href="route('books.create')" class="btn btn-primary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Tambah Buku
        </Link>
      </div>
    </template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="card p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
          <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Judul Buku</div>
          <div class="text-2xl font-bold text-slate-800 dark:text-white">{{ stats.total_books.toLocaleString() }}</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-500/10 flex items-center justify-center flex-shrink-0">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#0ea5e9" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" /></svg>
        </div>
        <div>
          <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Total Eksemplar</div>
          <div class="text-2xl font-bold text-slate-800 dark:text-white">{{ stats.total_copies.toLocaleString() }}</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Tersedia</div>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.available.toLocaleString() }}</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/10 flex items-center justify-center flex-shrink-0">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f43f5e" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
        <div>
          <div class="text-xs text-slate-500 uppercase tracking-wide font-medium">Sedang Dipinjam</div>
          <div class="text-2xl font-bold text-rose-500 dark:text-rose-400">{{ stats.borrowed.toLocaleString() }}</div>
        </div>
      </div>
    </div>

    <!-- Filter + Search Bar (1 baris) -->
    <div class="card px-4 py-3 mb-4">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-1 shrink-0">Filter</span>

        <select v-model="filters.category_id" @change="applyFilter" class="form-input py-1.5 px-3 text-sm w-auto min-w-[140px] max-w-[180px] shrink-0">
          <option value="">Semua Kategori</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>

        <select v-model="filters.availability" @change="applyFilter" class="form-input py-1.5 px-3 text-sm w-auto min-w-[150px] max-w-[180px] shrink-0">
          <option value="">Ketersediaan: Semua</option>
          <option value="tersedia">Tersedia</option>
          <option value="habis">Habis</option>
        </select>

        <!-- Divider -->
        <div class="h-5 w-px bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>

        <select v-model="filters.sort" @change="applyFilter" class="form-input py-1.5 px-3 text-sm w-auto min-w-[140px] max-w-[180px] shrink-0">
          <option value="terbaru">Urutkan: Terbaru</option>
          <option value="terpopuler">Urutkan: Terpopuler</option>
          <option value="judul">Urutkan: Judul A-Z</option>
        </select>

        <!-- Search memakan sisa ruang -->
        <div class="relative flex-1 min-w-0 ml-1">
          <input v-model="filters.search" @keyup.enter="applyFilter"
                 type="text" placeholder="🔍 Cari judul, pengarang, ISBN..."
                 class="form-input px-3 py-1.5 text-sm w-full outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden" v-if="books.data.length > 0">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-700">
              <th class="py-3 px-4 font-medium w-12 text-center">No</th>
              <th class="py-3 px-4 font-medium w-16">Cover</th>
              <th class="py-3 px-4 font-medium">Buku</th>
              <th class="py-3 px-4 font-medium">Kategori</th>
              <th class="py-3 px-4 font-medium text-center">Stok</th>
              <th class="py-3 px-4 font-medium text-center w-28">Rating</th>
              <th class="py-3 px-4 font-medium text-center w-24">Pinjam</th>
              <th class="py-3 px-4 font-medium text-center w-36">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr v-for="(book, index) in books.data" :key="book.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">

              <!-- No -->
              <td class="py-3 px-4 text-center text-sm font-medium text-slate-500 dark:text-slate-400">
                {{ books.from + index }}
              </td>

              <!-- Cover -->
              <td class="py-3 px-4">
                <div class="w-11 h-14 rounded-md overflow-hidden flex-shrink-0 flex items-center justify-center shadow-sm"
                     :style="`background: linear-gradient(135deg, ${coverGradient(book.category_id)})`">
                  <svg width="18" height="18" fill="none" viewBox="0 0 24 24" class="opacity-50">
                    <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </td>

              <!-- Judul + Penulis -->
              <td class="py-3 px-4">
                <div class="font-semibold text-slate-800 dark:text-slate-100 line-clamp-1" :title="book.title">{{ book.title }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ book.author }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5" v-if="book.publisher">
                  {{ book.publisher }} <span v-if="book.year">({{ book.year }})</span>
                </div>
              </td>

              <!-- Kategori -->
              <td class="py-3 px-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wide"
                      :style="`background:${categoryBg(book.category?.name)}20; color:${categoryBg(book.category?.name)}`">
                  {{ book.category?.name || '-' }}
                </span>
                <!-- <div v-if="book.classification_number" class="text-[11px] text-slate-400 mt-1">{{ book.classification_number }}</div> -->
              </td>

              <!-- Stok -->
              <td class="py-3 px-4 text-center">
                <span class="text-sm font-bold" :class="book.available_copies > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400'">
                  {{ book.available_copies }}
                </span>
                <!-- <div class="text-[11px] text-slate-400 leading-tight">tersedia</div>
                <div class="text-[11px] text-slate-400 leading-tight">dari {{ book.total_copies }} total</div> -->
              </td>

              <!-- Rating: 5 bintang -->
              <td class="py-3 px-4 text-center">
                <div class="flex items-center gap-1 justify-center">
                  <template v-for="n in 5" :key="n">
                    <svg width="12" height="12" viewBox="0 0 20 20"
                         :fill="n <= Math.round(book.ratings_avg_rating || 0) ? '#f59e0b' : '#e2e8f0'">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                    </svg>
                  </template>
                  <span class="text-xs text-slate-500 ml-1">{{ book.ratings_avg_rating ? Number(book.ratings_avg_rating).toFixed(1) : '—' }}</span>
                </div>
              </td>

              <!-- Total Dipinjam -->
              <td class="py-3 px-4 text-center">
                <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                  {{ book.total_loans ? Number(book.total_loans).toLocaleString() : '0' }}
                </div>
                <div class="text-[11px] text-slate-400">x dipinjam</div>
              </td>

      <!-- Aksi: ikon dengan border -->
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button @click="openDetail(book)"
                          class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 dark:border-slate-700 dark:hover:border-indigo-500 transition-colors"
                          title="Lihat Detail">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                  </button>
                  <Link :href="route('books.edit', book.id)"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 dark:border-slate-700 dark:hover:border-amber-500 transition-colors"
                        title="Edit Buku">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg>
                  </Link>
                  <button @click="confirmDelete(book)"
                          class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50 dark:border-slate-700 dark:hover:border-rose-500 transition-colors"
                          title="Hapus Buku">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Table (Info & Pagination) -->
      <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Info & Per Page -->
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
          <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select v-model="filters.per_page" @change="applyFilter" class="form-input py-1 px-2 text-sm w-16">
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span>baris</span>
          </div>
          <span v-if="books.total > 0">
            Menampilkan <strong class="text-slate-800 dark:text-slate-200">{{ books.from }}–{{ books.to }}</strong> dari <strong class="text-slate-800 dark:text-slate-200">{{ books.total.toLocaleString() }}</strong> buku
          </span>
        </div>

        <!-- Pagination Links -->
        <div v-if="books.last_page > 1" class="flex items-center gap-1">
          <Link v-if="books.prev_page_url" :href="books.prev_page_url"
                class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-500 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-700 text-sm">
            ‹
          </Link>
          <template v-for="page in paginationPages" :key="page">
            <span v-if="page === '...'" class="w-7 h-7 flex items-center justify-center text-slate-400 text-sm">…</span>
            <Link v-else :href="`${books.path}?page=${page}&${queryString}`"
                  class="w-7 h-7 flex items-center justify-center rounded text-sm font-medium transition-colors"
                  :class="page === books.current_page
                    ? 'bg-indigo-600 text-white'
                    : 'border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300'">
              {{ page }}
            </Link>
          </template>
          <Link v-if="books.next_page_url" :href="books.next_page_url"
                class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-500 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-700 text-sm">
            ›
          </Link>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="books.data.length === 0" class="card py-16 text-center">
      <svg width="48" height="48" fill="none" viewBox="0 0 24 24" class="mx-auto mb-4 text-slate-300 dark:text-slate-600">
        <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <div class="text-slate-500 dark:text-slate-400 font-medium">Belum ada buku ditemukan</div>
      <p class="text-sm text-slate-400 mt-1">Coba ubah filter atau kata kunci pencarian</p>
      <Link :href="route('books.create')" class="btn btn-primary mt-6">Tambah Buku Pertama</Link>
    </div>

    <!-- Import Modal -->
    <div v-if="isImportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden dark:bg-slate-800">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
          <h3 class="font-semibold text-lg text-slate-800 dark:text-white">Import Data Buku</h3>
          <button @click="isImportModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <form @submit.prevent="submitImport" class="p-6">
          <p class="text-sm text-slate-500 mb-4 dark:text-slate-400">
            Unggah file Excel (.xlsx, .xls) atau CSV dengan kolom: No Klasifikasi, Jml Buku, Judul Buku, Penerbit, Kota, Tahun Terbit, ISBN, Penulis, Perolehan, Diterima Tanggal, Tahun, Kategori.
          </p>
          <div class="form-group">
            <label class="form-label dark:text-slate-300">File Excel / CSV</label>
            <input type="file" @input="importForm.file = $event.target.files[0]" accept=".xlsx,.xls,.csv" class="form-input" required />
            <div v-if="importForm.errors.file" class="text-red-500 text-xs mt-1">{{ importForm.errors.file }}</div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="isImportModalOpen = false" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="importForm.processing">
              <span v-if="importForm.processing">Mengimpor...</span>
              <span v-else>Upload &amp; Import</span>
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
            <h3 class="font-semibold text-slate-800 dark:text-white">Hapus Buku</h3>
            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
          </div>
        </div>
        <p class="text-sm text-slate-600 dark:text-slate-300 mb-5">
          Yakin ingin menghapus buku <strong>"{{ deleteTarget.title }}"</strong>? Semua data eksemplar juga akan terhapus.
        </p>
        <div class="flex justify-end gap-3">
          <button @click="deleteTarget = null" class="btn btn-secondary">Batal</button>
          <button @click="doDelete" class="btn bg-rose-600 hover:bg-rose-700 text-white">Ya, Hapus</button>
        </div>
      </div>
    </div>

    <!-- Detail Book Modal -->
    <Transition name="fade">
    <div v-if="viewBook" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @click.self="closeDetail">
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto">

        <!-- Header Modal -->
        <div class="flex items-start gap-4 p-6 border-b border-slate-100 dark:border-slate-700">
          <div class="w-16 h-20 rounded-lg flex-shrink-0 flex items-center justify-center shadow"
               :style="`background: linear-gradient(135deg, ${coverGradient(viewBook.category_id)})`">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="opacity-50">
              <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ viewBook.title }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">{{ viewBook.author }}</p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase"
                    :style="`background:${categoryBg(viewBook.category?.name)}20; color:${categoryBg(viewBook.category?.name)}`">
                {{ viewBook.category?.name || '-' }}
              </span>
              <span v-if="viewBook.classification_number" class="text-xs text-slate-400">Klas: {{ viewBook.classification_number }}</span>
            </div>
          </div>
          <button @click="closeDetail" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 flex-shrink-0">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Stats Strip -->
        <div class="grid grid-cols-4 divide-x divide-slate-100 dark:divide-slate-700 border-b border-slate-100 dark:border-slate-700">
          <div class="py-4 px-5 text-center">
            <div class="text-xl font-bold" :class="viewBook.available_copies > 0 ? 'text-emerald-600' : 'text-rose-500'">{{ viewBook.available_copies }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Tersedia</div>
          </div>
          <div class="py-4 px-5 text-center">
            <div class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ viewBook.total_copies }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Total Eks.</div>
          </div>
          <div class="py-4 px-5 text-center">
            <div class="text-xl font-bold text-amber-500">{{ viewBook.ratings_avg_rating ? Number(viewBook.ratings_avg_rating).toFixed(1) : '—' }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">⭐ Rating</div>
          </div>
          <div class="py-4 px-5 text-center">
            <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ viewBook.total_loans || 0 }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">× Dipinjam</div>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Info Buku -->
          <div>
            <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Informasi Buku</h4>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
              <div v-if="viewBook.isbn"><dt class="text-slate-400">ISBN</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.isbn }}</dd></div>
              <div v-if="viewBook.publisher"><dt class="text-slate-400">Penerbit</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.publisher }}</dd></div>
              <div v-if="viewBook.city"><dt class="text-slate-400">Kota</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.city }}</dd></div>
              <div v-if="viewBook.year"><dt class="text-slate-400">Tahun Terbit</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.year }}</dd></div>
              <div v-if="viewBook.acquisition_type"><dt class="text-slate-400">Perolehan</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.acquisition_type }}</dd></div>
              <div v-if="viewBook.inventory_year"><dt class="text-slate-400">Tahun Inventaris</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ viewBook.inventory_year }}</dd></div>
            </dl>
            <div v-if="viewBook.description" class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
              <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Deskripsi</h4>
              <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ viewBook.description }}</p>
            </div>
          </div>

          <!-- Daftar Eksemplar -->
          <div>
            <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Daftar Eksemplar Fisik</h4>

            <!-- Loading -->
            <div v-if="loadingDetail" class="flex items-center justify-center py-10 gap-3 text-slate-400">
              <svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Memuat data eksemplar...
            </div>

            <!-- Tabel Eksemplar -->
            <div v-else-if="viewBookCopies && viewBookCopies.length > 0" class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-100 text-xs text-slate-500 uppercase tracking-wider dark:bg-slate-800/50 dark:border-slate-700">
                    <th class="py-2.5 px-4 font-medium"># Kode</th>
                    <th class="py-2.5 px-4 font-medium">Barcode</th>
                    <th class="py-2.5 px-4 font-medium">Kondisi</th>
                    <th class="py-2.5 px-4 font-medium text-right">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                  <tr v-for="copy in viewBookCopies" :key="copy.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="py-2.5 px-4 font-mono text-xs text-indigo-600 dark:text-indigo-400 font-semibold whitespace-nowrap">{{ copy.copy_code }}</td>
                    <td class="py-2.5 px-4">
                      <div class="bg-white rounded-md p-1 inline-block shadow-sm">
                        <svg class="modal-barcode-svg" :data-barcode="copy.barcode" style="max-height:42px; width:auto;"></svg>
                      </div>
                    </td>
                    <td class="py-2.5 px-4">
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                            :class="copy.condition === 'baik' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'">
                        {{ copy.condition }}
                      </span>
                    </td>
                    <td class="py-2.5 px-4 text-right">
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                            :class="copy.status === 'tersedia' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'">
                        {{ copy.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else-if="!loadingDetail" class="text-center py-6 text-slate-400 text-sm">
              Belum ada eksemplar fisik untuk buku ini.
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 pb-6 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-4">
          <button @click="closeDetail" class="btn btn-secondary">Tutup</button>
          <Link :href="route('books.edit', viewBook.id)" class="btn btn-secondary">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" /></svg>
            Edit Buku
          </Link>
        </div>
      </div>
    </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, nextTick, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import JsBarcode from 'jsbarcode'

const props = defineProps({
  books:      Object,
  categories: Array,
  filters:    Object,
  stats:      Object,
})

// ── Filters ────────────────────────────────────────────────
const filters = reactive({
  search:       props.filters?.search       || '',
  category_id:  props.filters?.category_id  || '',
  availability: props.filters?.availability || '',
  sort:         props.filters?.sort         || 'terbaru',
  per_page:     props.filters?.per_page     || '20',
})

function applyFilter() {
  router.get(route('books.index'), filters, { preserveState: true, replace: true })
}

const queryString = computed(() => {
  const p = new URLSearchParams()
  if (filters.search)       p.set('search',       filters.search)
  if (filters.category_id)  p.set('category_id',  filters.category_id)
  if (filters.availability) p.set('availability', filters.availability)
  if (filters.sort && filters.sort !== 'terbaru') p.set('sort', filters.sort)
  return p.toString()
})

// ── Pagination pages (sliding window) ──────────────────────
const paginationPages = computed(() => {
  const current = props.books.current_page
  const last    = props.books.last_page
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)

  const pages = []
  pages.push(1)
  if (current > 4) pages.push('...')
  for (let i = Math.max(2, current - 2); i <= Math.min(last - 1, current + 2); i++) pages.push(i)
  if (current < last - 3) pages.push('...')
  pages.push(last)
  return pages
})

// ── Cover colors ──────────────────────────────────────────
const gradients = [
  ['#6366f1', '#8b5cf6'],
  ['#0ea5e9', '#6366f1'],
  ['#10b981', '#059669'],
  ['#f59e0b', '#ef4444'],
  ['#ec4899', '#8b5cf6'],
  ['#14b8a6', '#0ea5e9'],
  ['#f97316', '#ec4899'],
]
function coverGradient(id) {
  const [a, b] = gradients[((id || 0) - 1) % gradients.length]
  return `${a}, ${b}`
}

const categoryColors = {
  'fiksi':     '#10b981',
  'non fiksi': '#6366f1',
  'sastra':    '#8b5cf6',
  'sejarah':   '#f59e0b',
  'sains':     '#0ea5e9',
}
function categoryBg(name) {
  if (!name) return '#94a3b8'
  return categoryColors[name.toLowerCase()] || '#6366f1'
}

// ── Import ────────────────────────────────────────────────
const isImportModalOpen = ref(false)
const importForm = useForm({ file: null })

function submitImport() {
  importForm.post(route('books.import'), {
    onSuccess: () => { isImportModalOpen.value = false; importForm.reset() },
  })
}

// ── View Detail Modal ─────────────────────────────────────
const viewBook       = ref(null)
const viewBookCopies = ref(null)
const loadingDetail  = ref(false)

async function openDetail(book) {
  viewBook.value       = book
  viewBookCopies.value = null
  loadingDetail.value  = true
  try {
    const res  = await fetch(`/books/${book.id}/detail`)
    const data = await res.json()
    viewBookCopies.value = data.copies || []
    loadingDetail.value  = false   // ← harus SEBELUM nextTick agar tabel sudah di DOM
    await nextTick()
    renderModalBarcodes()
  } catch (e) {
    viewBookCopies.value = []
    loadingDetail.value  = false
  }
}

function closeDetail() {
  viewBook.value       = null
  viewBookCopies.value = null
}

function renderModalBarcodes() {
  document.querySelectorAll('.modal-barcode-svg').forEach(el => {
    if (!el.dataset.barcode) return
    JsBarcode(el, el.dataset.barcode, {
      format: 'CODE128', height: 28, displayValue: true, fontSize: 10, margin: 0
    })
  })
}

// ── Delete ────────────────────────────────────────────────
const deleteTarget = ref(null)
function confirmDelete(book) { deleteTarget.value = book }
function doDelete() {
  router.delete(route('books.destroy', deleteTarget.value.id), {
    onSuccess: () => { deleteTarget.value = null }
  })
}
</script>
