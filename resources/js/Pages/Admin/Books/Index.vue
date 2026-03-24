<template>
  <AdminLayout title="Katalog Buku">
    <!-- Topbar actions -->
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <button @click="isImportModalOpen = true" class="btn btn-secondary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
          Import Excel
        </button>
        <button @click="openBookForm()" class="btn btn-primary">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Tambah Buku
        </button>
      </div>
    </template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="card p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2b5a41" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
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

    <div class="space-y-4 md:space-y-6">

      <!-- Rows per page, Filters & Search (separate card) -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Left: Rows per page -->
        <div class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
          <span>Tampilkan</span>
          <select v-model="filters.per_page" @change="applyFilter"
            class="px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>baris</span>
        </div>

        <!-- Right: Filters & Search -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
          <select v-model="filters.category_id" @change="applyFilter"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Kategori</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>

          <select v-model="filters.availability" @change="applyFilter"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="">Status: Semua</option>
            <option value="tersedia">Tersedia</option>
            <option value="habis">Habis</option>
          </select>

          <select v-model="filters.availability" @change="applyFilter"
            class="w-full md:w-auto px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
            <option value="" selected>Urutkan: Terbaru</option>
            <option value="terpopuler">Terpopuler</option>
            <option value="judul">Judul A-Z</option>
          </select>

          <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input v-model="filters.search" type="text"
              placeholder="Cari judul, ISBN..."
              class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
          </div>
        </div>
      </div>

      <!-- Table (separate card) -->
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="w-full min-w-[900px]">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">No</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Cover</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Buku</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Kategori</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Stok</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Rating</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Pinjam</th>
              <th class="px-3 md:px-6 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="books.data && books.data.length > 0" v-for="(book, index) in books.data" :key="book.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900">{{ books.from + index }}</td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="w-10 h-14 rounded overflow-hidden flex-shrink-0 flex items-center justify-center shadow-sm"
                     :style="`background: linear-gradient(135deg, ${coverGradient(book.category_id)})`">
                  <svg width="18" height="18" fill="none" viewBox="0 0 24 24" class="opacity-50">
                    <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="font-semibold text-gray-900 line-clamp-1" :title="book.title">{{ book.title }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ book.author }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5" v-if="book.publisher">
                  {{ book.publisher }} <span v-if="book.year">({{ book.year }})</span>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                      :style="`background:${categoryBg(book.category?.name)}15; color:${categoryBg(book.category?.name)}; border: 1px solid ${categoryBg(book.category?.name)}30;`">
                  {{ book.category?.name || '-' }}
                </span>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-bold" :class="book.available_copies > 0 ? 'text-emerald-600' : 'text-red-500'">
                {{ book.available_copies }}
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex items-center">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#f59e0b">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700 ml-1.5">{{ book.ratings_avg_rating ? Number(book.ratings_avg_rating).toFixed(1) : '—' }}</span>
                </div>
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-semibold text-gray-700">
                {{ book.total_loans ? Number(book.total_loans).toLocaleString() : '0' }}
              </td>
              <td class="px-3 md:px-6 py-3 md:py-4">
                <div class="flex gap-2">
                  <button @click="openDetail(book)" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-700 font-medium">Detail</button>
                  <button @click="openBookForm(book)" class="px-3 py-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">Edit</button>
                  <button @click="confirmDelete(book)" class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-medium">Hapus</button>
                </div>
              </td>
            </tr>
            <!-- Empty state -->
            <tr v-if="!books.data || books.data.length === 0">
              <td colspan="8" class="px-6 py-16 text-center">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" class="mx-auto mb-3 text-gray-300"><path stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                <div class="text-gray-500 font-medium">Belum ada buku ditemukan</div>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau kata kunci pencarian</p>
                <Link :href="route('books.create')" class="inline-block mt-4 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg text-sm transition">Tambah Buku Pertama</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (standalone, outside table card) -->
      <div v-if="books.data && books.data.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-semibold text-gray-800">{{ books.from }}</span> sampai <span class="font-semibold text-gray-800">{{ books.to }}</span> dari <span class="font-semibold text-gray-800">{{ books.total.toLocaleString() }}</span> buku
        </div>
        <div v-if="books.last_page > 1" class="flex items-center gap-1.5">
          <Link v-if="books.prev_page_url" :href="books.prev_page_url"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors bg-white border text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800">
            ‹
          </Link>
          <template v-for="page in paginationPages" :key="page">
            <span v-if="page === '...'" class="w-9 h-9 flex items-center justify-center text-gray-400 text-sm">…</span>
            <Link v-else :href="`${books.path}?page=${page}&${queryString}`"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors border"
              :class="page === books.current_page
                ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm'
                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800'">
              {{ page }}
            </Link>
          </template>
          <Link v-if="books.next_page_url" :href="books.next_page_url"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors bg-white border text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800">
            ›
          </Link>
        </div>
      </div>
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
                    <th class="py-2.5 px-4 font-medium">Status</th>
                    <th class="py-2.5 px-4 font-medium text-right">Aksi</th>
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
                    <!-- Kondisi: editable -->
                    <td class="py-2.5 px-4">
                      <select v-if="editingCopyId === copy.id" v-model="copyEditForm.condition"
                        class="px-2 py-1 text-xs rounded border border-gray-300 bg-white text-gray-900 focus:ring-1 focus:ring-emerald-500">
                        <option value="baik">baik</option>
                        <option value="rusak_ringan">rusak_ringan</option>
                        <option value="rusak_berat">rusak_berat</option>
                        <option value="hilang">hilang</option>
                      </select>
                      <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                            :class="copy.condition === 'baik' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                        {{ copy.condition }}
                      </span>
                    </td>
                    <!-- Status: editable -->
                    <td class="py-2.5 px-4">
                      <select v-if="editingCopyId === copy.id" v-model="copyEditForm.status"
                        class="px-2 py-1 text-xs rounded border border-gray-300 bg-white text-gray-900 focus:ring-1 focus:ring-emerald-500">
                        <option value="tersedia">tersedia</option>
                        <option value="dipinjam">dipinjam</option>
                        <option value="tidak_aktif">tidak_aktif</option>
                      </select>
                      <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                            :class="{ 'bg-indigo-50 text-indigo-700': copy.status === 'tersedia', 'bg-amber-50 text-amber-700': copy.status === 'dipinjam', 'bg-red-50 text-red-700': copy.status === 'tidak_aktif' }">
                        {{ copy.status }}
                      </span>
                    </td>
                    <!-- Aksi -->
                    <td class="py-2.5 px-4 text-right">
                      <div v-if="editingCopyId === copy.id" class="flex items-center justify-end gap-2">
                        <button @click="saveCopyEdit(copy)" class="text-emerald-600 hover:text-emerald-700 text-xs font-semibold">Simpan</button>
                        <button @click="editingCopyId = null" class="text-gray-400 hover:text-gray-600 text-xs font-semibold">Batal</button>
                      </div>
                      <button v-else @click="startCopyEdit(copy)" class="text-blue-500 hover:text-blue-700 text-xs font-semibold">Edit</button>
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

        <!-- Tambah Eksemplar Section -->
        <div class="px-6 pb-4">
          <button v-if="!showAddCopyForm" @click="initAddCopy()" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-xl text-sm text-gray-500 hover:border-emerald-400 hover:text-emerald-600 transition font-medium">
            + Tambah Eksemplar
          </button>
          <div v-else class="p-4 border border-gray-200 rounded-xl bg-gray-50 space-y-3">
            <h4 class="text-sm font-bold text-gray-700">Tambah Eksemplar Baru</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kode Eksemplar</label>
                <input type="text" v-model="addCopyForm.copy_code" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" placeholder="BK-0001-C1" />
                <div v-if="addCopyForm.errors.copy_code" class="text-xs text-red-500 mt-1">{{ addCopyForm.errors.copy_code }}</div>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Barcode</label>
                <input type="text" v-model="addCopyForm.barcode" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" placeholder="BC58009..." />
                <div v-if="addCopyForm.errors.barcode" class="text-xs text-red-500 mt-1">{{ addCopyForm.errors.barcode }}</div>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi</label>
                <select v-model="addCopyForm.condition" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
                  <option value="baik">Baik</option>
                  <option value="rusak_ringan">Rusak Ringan</option>
                  <option value="rusak_berat">Rusak Berat</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button @click="showAddCopyForm = false" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">Batal</button>
              <button @click="submitAddCopy()" :disabled="addCopyForm.processing" class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg text-sm transition disabled:opacity-50">
                {{ addCopyForm.processing ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 pb-6 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-4">
          <button @click="closeDetail" class="btn btn-secondary">Tutup</button>
        </div>
      </div>
    </div>
    </Transition>

    <!-- Create / Edit Book Modal -->
    <div v-if="bookFormModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="bookFormModal = false">
      <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-emerald-500 to-emerald-600">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ editingBook ? 'Edit Buku' : 'Tambah Buku Baru' }}</h3>
          </div>
          <button @click="bookFormModal = false" class="text-gray-400 hover:text-gray-600">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <form @submit.prevent="submitBookForm" class="px-6 py-5 overflow-y-auto space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
              <input type="text" v-model="bookForm.title" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
              <div v-if="bookForm.errors.title" class="text-xs text-red-500 mt-1">{{ bookForm.errors.title }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
              <input type="text" v-model="bookForm.author" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
              <div v-if="bookForm.errors.author" class="text-xs text-red-500 mt-1">{{ bookForm.errors.author }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
              <select v-model="bookForm.category_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500">
                <option value="">Pilih Kategori...</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <div v-if="bookForm.errors.category_id" class="text-xs text-red-500 mt-1">{{ bookForm.errors.category_id }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
              <input type="text" v-model="bookForm.isbn" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
              <div v-if="bookForm.errors.isbn" class="text-xs text-red-500 mt-1">{{ bookForm.errors.isbn }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
              <input type="text" v-model="bookForm.publisher" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No Klasifikasi</label>
              <input type="text" v-model="bookForm.classification_number" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kota Penerbit</label>
              <input type="text" v-model="bookForm.city" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
              <input type="text" v-model="bookForm.year" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Perolehan</label>
              <input type="text" v-model="bookForm.acquisition_type" placeholder="Misal: Beli, Sumbangan" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Inventaris</label>
              <input type="text" v-model="bookForm.inventory_year" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Sinopsis</label>
            <textarea v-model="bookForm.description" rows="3" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500" placeholder="Tambahkan informasi rinci tentang buku ini..."></textarea>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="button" @click="bookFormModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">Batal</button>
            <button type="submit" :disabled="bookForm.processing" class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg text-sm transition disabled:opacity-50">
              {{ bookForm.processing ? 'Menyimpan...' : (editingBook ? 'Simpan Perubahan' : 'Tambah Buku') }}
            </button>
          </div>
        </form>
      </div>
    </div>
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
  sort:         props.filters?.sort         || '',
  per_page:     props.filters?.per_page     || '20',
})

function applyFilter() {
  router.get(route('books.index'), filters, { preserveState: true, replace: true })
}

let searchTimer = null
watch(() => filters.search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => applyFilter(), 400)
})

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
  ['#2b5a41', '#1c3b2b'],
  ['#0ea5e9', '#2b5a41'],
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
  'non fiksi': '#2b5a41',
  'sastra':    '#8b5cf6',
  'sejarah':   '#f59e0b',
  'sains':     '#0ea5e9',
}
function categoryBg(name) {
  if (!name) return '#94a3b8'
  return categoryColors[name.toLowerCase()] || '#2b5a41'
}

// ── Import ────────────────────────────────────────────────
const isImportModalOpen = ref(false)
const importForm = useForm({ file: null })

function submitImport() {
  importForm.post(route('books.import'), {
    onSuccess: () => { isImportModalOpen.value = false; importForm.reset() },
  })
}

// ── Create / Edit Book Modal ──────────────────────────────
const bookFormModal = ref(false)
const editingBook = ref(null)
const bookForm = useForm({
  title: '', author: '', category_id: '', isbn: '',
  publisher: '', classification_number: '', city: '',
  year: '', acquisition_type: '', inventory_year: '', description: ''
})

function openBookForm(book = null) {
  editingBook.value = book
  if (book) {
    bookForm.title = book.title || ''
    bookForm.author = book.author || ''
    bookForm.category_id = book.category_id || ''
    bookForm.isbn = book.isbn || ''
    bookForm.publisher = book.publisher || ''
    bookForm.classification_number = book.classification_number || ''
    bookForm.city = book.city || ''
    bookForm.year = book.year || ''
    bookForm.acquisition_type = book.acquisition_type || ''
    bookForm.inventory_year = book.inventory_year || ''
    bookForm.description = book.description || ''
  } else {
    bookForm.reset()
  }
  bookForm.clearErrors()
  bookFormModal.value = true
}

function submitBookForm() {
  if (editingBook.value) {
    bookForm.put(route('books.update', editingBook.value.id), {
      onSuccess: () => { bookFormModal.value = false; bookForm.reset() },
    })
  } else {
    bookForm.post(route('books.store'), {
      onSuccess: () => { bookFormModal.value = false; bookForm.reset() },
    })
  }
}

// ── Add Copy Form ─────────────────────────────────────────
const showAddCopyForm = ref(false)
const addCopyForm = useForm({ copy_code: '', barcode: '', condition: 'baik' })

function initAddCopy() {
  const bookCode = viewBook.value?.book_code || `BK-${String(viewBook.value?.id).padStart(4, '0')}`
  const nextIdx = (viewBookCopies.value?.length || 0) + 1
  addCopyForm.copy_code = `${bookCode}-C${nextIdx}`
  addCopyForm.barcode = 'BC' + Math.floor(10000000 + Math.random() * 90000000)
  addCopyForm.condition = 'baik'
  addCopyForm.clearErrors()
  showAddCopyForm.value = true
}

function submitAddCopy() {
  addCopyForm.post(route('books.copies.store', viewBook.value.id), {
    preserveState: true,
    onSuccess: async () => {
      showAddCopyForm.value = false
      addCopyForm.reset()
      // refresh copies list
      const res = await fetch(route('books.detail', viewBook.value.id))
      const data = await res.json()
      viewBookCopies.value = data.copies || []
    },
  })
}

// ── Inline Copy Edit ──────────────────────────────────────
const editingCopyId = ref(null)
const copyEditForm = reactive({ condition: '', status: '' })

function startCopyEdit(copy) {
  editingCopyId.value = copy.id
  copyEditForm.condition = copy.condition
  copyEditForm.status = copy.status
}

function saveCopyEdit(copy) {
  router.put(route('copies.update', copy.id), {
    condition: copyEditForm.condition,
    status: copyEditForm.status,
  }, {
    preserveState: true,
    onSuccess: () => {
      copy.condition = copyEditForm.condition
      copy.status = copyEditForm.status
      editingCopyId.value = null
    },
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
