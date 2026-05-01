<template>
  <PublicLayout>
    <!-- Page Header -->
    <div class="catalog-header">
      <div class="catalog-header-inner">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
          <Link :href="route('home')" class="breadcrumb-link">Beranda</Link>
          <span class="breadcrumb-sep">›</span>
          <span class="breadcrumb-current">Katalog Buku</span>
        </div>
        <div class="flex items-end justify-between gap-6 mt-4">
          <div>
            <h1 class="catalog-title">Katalog Buku</h1>
            <p class="catalog-sub">{{ books.total.toLocaleString('id-ID') }} buku tersedia</p>
          </div>
          <!-- Search Bar -->
          <form @submit.prevent="applyFilters" class="catalog-search">
            <div class="search-wrap">
              <svg class="search-ico" width="16" height="16" fill="none" viewBox="0 0 24 24">
                <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input v-model="localFilters.search" type="text"
                placeholder="Cari buku yang Anda inginkan..."
                class="catalog-search-input" />
            </div>
            <button type="submit" class="catalog-search-btn">Cari</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Main -->
    <div class="catalog-body">
      <div class="catalog-inner">

        <!-- Sidebar Filter -->
        <aside class="filter-sidebar">
          <!-- Active Filters -->
          <div v-if="hasActiveFilter" class="filter-section">
            <div class="filter-group-title">Filter Aktif</div>
            <div class="active-tags">
              <span v-if="localFilters.search" class="active-tag">
                "{{ localFilters.search }}"
                <button @click="clearFilter('search')">×</button>
              </span>
              <span v-if="localFilters.category" class="active-tag">
                {{ getCategoryName(localFilters.category) }}
                <button @click="clearFilter('category')">×</button>
              </span>
              <span v-if="localFilters.availability" class="active-tag">
                {{ localFilters.availability === 'tersedia' ? 'Tersedia' : 'Dipinjam' }}
                <button @click="clearFilter('availability')">×</button>
              </span>
              <button @click="clearAll" class="clear-all-btn">Hapus Semua</button>
            </div>
          </div>

          <!-- Kategori -->
          <div class="filter-section">
            <div class="filter-group-title">Kategori</div>
            <div class="filter-options">
              <label class="filter-radio">
                <input type="radio" v-model="localFilters.category" value="" @change="applyFilters"/>
                <span>Semua Kategori</span>
              </label>
              <label v-for="cat in categories" :key="cat.id" class="filter-radio">
                <input type="radio" v-model="localFilters.category"
                  :value="String(cat.id)" @change="applyFilters"/>
                <span>{{ cat.name }}</span>
                <span class="filter-count">{{ cat.books_count }}</span>
              </label>
            </div>
          </div>

          <!-- Ketersediaan -->
          <div class="filter-section">
            <div class="filter-group-title">Ketersediaan</div>
            <div class="filter-options">
              <label class="filter-radio">
                <input type="radio" v-model="localFilters.availability" value="" @change="applyFilters"/>
                <span>Semua</span>
              </label>
              <label class="filter-radio">
                <input type="radio" v-model="localFilters.availability" value="tersedia" @change="applyFilters"/>
                <span>Tersedia</span>
              </label>
              <label class="filter-radio">
                <input type="radio" v-model="localFilters.availability" value="dipinjam" @change="applyFilters"/>
                <span>Dipinjam</span>
              </label>
            </div>
          </div>

          <!-- Apply -->
          <button @click="applyFilters" class="apply-btn">Terapkan Filter</button>
        </aside>

        <!-- Book Grid -->
        <div class="catalog-content">
          <!-- Result info -->
          <div class="result-bar">
            <span class="result-info">
              Menampilkan {{ books.from }}–{{ books.to }} dari {{ books.total.toLocaleString('id-ID') }} buku
            </span>
          </div>

          <!-- Grid -->
          <div v-if="books.data.length > 0" class="pub-books-grid">
            <div v-for="book in books.data" :key="book.id" class="pub-book-card" @click="openModal(book)">
              <!-- Cover -->
              <div class="pub-book-cover">
                <img v-if="book.cover_image" 
                  :src="book.cover_image.startsWith('http') ? book.cover_image : '/' + book.cover_image"
                  :alt="book.title" class="pub-book-img" />
                <div v-else class="pub-book-placeholder">
                  <svg width="28" height="28" fill="none" viewBox="0 0 24 24">
                    <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                      stroke="#25a07e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <span class="pub-cat-badge">{{ book.category }}</span>
                <!-- Hover overlay -->
                <div class="pub-book-overlay">
                  <span class="pub-book-overlay-text">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px">
                      <path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Lihat Detail
                  </span>
                </div>
              </div>
              <!-- Info -->
              <div class="pub-book-info">
                <div class="pub-book-title">{{ book.title }}</div>
                <div class="pub-book-author">{{ book.author }}</div>
                <div class="pub-book-meta">
                  <span class="pub-rack">{{ book.rack_number }}</span>
                  <span class="pub-avail" :class="book.available_count > 0 ? 'avail' : 'borr'">
                    {{ book.available_count > 0 ? 'Tersedia' : 'Dipinjam' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Book Detail Modal ────────────────────────────── -->
          <Teleport to="body">
            <Transition name="modal">
              <div v-if="selectedBook" class="book-modal-backdrop" @click.self="closeModal">
                <div class="book-modal">
                  <!-- Close Button -->
                  <button class="modal-close" @click="closeModal" aria-label="Tutup">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                      <path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                  </button>

                  <div class="modal-inner">
                    <!-- Left: Cover -->
                    <div class="modal-cover-col">
                      <div class="modal-cover-wrap">
                        <img v-if="selectedBook.cover_image"
                          :src="selectedBook.cover_image.startsWith('http') ? selectedBook.cover_image : '/' + selectedBook.cover_image"
                          :alt="selectedBook.title" class="modal-cover-img" />
                        <div v-else class="modal-cover-placeholder">
                          <svg width="56" height="56" fill="none" viewBox="0 0 24 24">
                            <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                              stroke="#25a07e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </div>
                      </div>
                      <!-- Status badge -->
                      <div class="modal-status-badge" :class="selectedBook.available_count > 0 ? 'status-avail' : 'status-borr'">
                        <span class="status-dot"></span>
                        {{ selectedBook.available_count > 0 ? 'Tersedia' : 'Sedang Dipinjam' }}
                      </div>
                      <div v-if="selectedBook.available_count > 0" class="modal-stock">
                        {{ selectedBook.available_count }} eksemplar tersedia
                      </div>
                    </div>

                    <!-- Right: Detail -->
                    <div class="modal-detail-col">
                      <!-- Category badge -->
                      <span class="modal-cat-badge">{{ selectedBook.category }}</span>

                      <!-- Title & Author -->
                      <h2 class="modal-title">{{ selectedBook.title }}</h2>
                      <p class="modal-author">{{ selectedBook.author }}</p>

                      <!-- Metadata grid -->
                      <div class="modal-meta-grid">
                        <div v-if="selectedBook.publisher" class="meta-item">
                          <span class="meta-label">Penerbit</span>
                          <span class="meta-value">{{ selectedBook.publisher }}</span>
                        </div>
                        <div v-if="selectedBook.year" class="meta-item">
                          <span class="meta-label">Tahun Terbit</span>
                          <span class="meta-value">{{ selectedBook.year }}</span>
                        </div>
                        <div v-if="selectedBook.isbn" class="meta-item">
                          <span class="meta-label">ISBN</span>
                          <span class="meta-value">{{ selectedBook.isbn }}</span>
                        </div>
                        <div v-if="selectedBook.pages" class="meta-item">
                          <span class="meta-label">Jumlah Halaman</span>
                          <span class="meta-value">{{ selectedBook.pages }} halaman</span>
                        </div>
                        <div v-if="selectedBook.edition" class="meta-item">
                          <span class="meta-label">Edisi</span>
                          <span class="meta-value">{{ selectedBook.edition }}</span>
                        </div>
                        <div v-if="selectedBook.language" class="meta-item">
                          <span class="meta-label">Bahasa</span>
                          <span class="meta-value">{{ selectedBook.language }}</span>
                        </div>
                        <div v-if="selectedBook.city" class="meta-item">
                          <span class="meta-label">Kota Terbit</span>
                          <span class="meta-value">{{ selectedBook.city }}</span>
                        </div>
                        <div v-if="selectedBook.rack_number" class="meta-item">
                          <span class="meta-label">Nomor Rak</span>
                          <span class="meta-value">{{ selectedBook.rack_number }}</span>
                        </div>
                        <div v-if="selectedBook.total_loans" class="meta-item">
                          <span class="meta-label">Total Dipinjam</span>
                          <span class="meta-value">{{ selectedBook.total_loans }} kali</span>
                        </div>
                      </div>

                      <!-- Divider -->
                      <div class="modal-divider"></div>

                      <!-- Synopsis / Description -->
                      <div class="modal-synopsis-section">
                        <h3 class="modal-synopsis-title">Sinopsis</h3>
                        <p v-if="selectedBook.description" class="modal-synopsis-text">
                          {{ selectedBook.description }}
                        </p>
                        <p v-else class="modal-synopsis-empty">
                          Buku ini tidak memiliki sinopsis. Namun, dipastikan buku ini
                          sangat menarik untuk dibaca dan menambah wawasan Anda di perpustakaan kami.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
          </Teleport>

          <!-- Empty State -->
          <div v-if="books.data.length === 0" class="pub-empty">
            <svg width="56" height="56" fill="none" viewBox="0 0 24 24">
              <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                stroke="#98a2b3" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="pub-empty-title">Buku tidak ditemukan</div>
            <p class="pub-empty-sub">Coba ubah kata kunci atau filter yang digunakan</p>
            <button @click="clearAll" class="pub-empty-btn">Hapus Filter</button>
          </div>

          <!-- Pagination -->
          <div v-if="books.last_page > 1" class="pub-pagination">
            <Link v-if="books.prev_page_url" :href="books.prev_page_url"
              class="page-btn">← Sebelumnya</Link>
            <div class="page-info">
              Halaman {{ books.current_page }} dari {{ books.last_page }}
            </div>
            <Link v-if="books.next_page_url" :href="books.next_page_url"
              class="page-btn">Selanjutnya →</Link>
          </div>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  books:      Object,
  categories: Array,
  filters:    Object,
})

const localFilters = reactive({
  search:       props.filters?.search       || '',
  category:     props.filters?.category     ? String(props.filters.category) : '',
  availability: props.filters?.availability || '',
})

const hasActiveFilter = computed(() =>
  localFilters.search || localFilters.category || localFilters.availability
)

function applyFilters() {
  const params = {}
  if (localFilters.search)       params.search = localFilters.search
  if (localFilters.category)     params.category = localFilters.category
  if (localFilters.availability) params.availability = localFilters.availability
  router.get(route('catalog'), params, { preserveState: true, replace: true })
}

function clearFilter(key) {
  localFilters[key] = ''
  applyFilters()
}

function clearAll() {
  localFilters.search = ''
  localFilters.category = ''
  localFilters.availability = ''
  applyFilters()
}

function getCategoryName(id) {
  return props.categories.find(c => String(c.id) === String(id))?.name || id
}

// ── Modal ────────────────────────────────────────────────────
const selectedBook = ref(null)

function openModal(book) {
  selectedBook.value = book
  document.body.style.overflow = 'hidden'
}

function closeModal() {
  selectedBook.value = null
  document.body.style.overflow = ''
}
</script>

<style scoped>
/* ─── Header ─────────────────────────────────────────────── */
.catalog-header {
  background: #faf8f3;
  border-bottom: 1px solid #d8f3e3;
  padding: 24px 0;
}

.catalog-header-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #667085;
}

.breadcrumb-link {
  color: #667085;
  text-decoration: none;
}

.breadcrumb-link:hover { color: #2d9e64; }
.breadcrumb-sep { color: #d0d5dd; }
.breadcrumb-current { color: #344054; font-weight: 500; }

.catalog-title {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 30px;
  font-weight: 500;
  color: #1a1f1c;
}

.catalog-sub {
  font-size: 14px;
  color: #8a9490;
  margin-top: 2px;
}

/* Catalog Search */
.catalog-search {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.search-wrap {
  position: relative;
}

.search-ico {
  position: absolute;
  left: 11px;
  top: 50%;
  transform: translateY(-50%);
  color: #98a2b3;
}

.catalog-search-input {
  width: 320px;
  padding: 9px 12px 9px 36px;
  border: 1.5px solid #d8f3e3;
  border-radius: 2rem;
  font-size: 14px;
  outline: none;
  font-family: 'Outfit', sans-serif;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.catalog-search-input:focus {
  border-color: #2d9e64;
  box-shadow: 0 0 0 3px rgba(45, 158, 100, 0.12);
}

.catalog-search-btn {
  padding: 9px 20px;
  background: #2d9e64;
  color: white;
  border: none;
  border-radius: 2rem;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
  transition: background 0.15s;
}

.catalog-search-btn:hover { background: #1f7a4a; }

/* ─── Body ─────────────────────────────────────────────────── */
.catalog-body {
  padding: 32px 24px;
  min-height: calc(100vh - 200px);
  background: #faf8f3;
}

.catalog-inner {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 220px 1fr;
  gap: 32px;
  align-items: start;
}

/* ─── Filter Sidebar ─────────────────────────────────────── */
.filter-sidebar {
  background: #fff;
  border: 1px solid #d8f3e3;
  border-radius: 16px;
  padding: 20px;
  position: sticky;
  top: 80px;
}

.filter-section {
  margin-bottom: 24px;
}

.filter-section:last-of-type {
  margin-bottom: 0;
}

.filter-group-title {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #344054;
  margin-bottom: 12px;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 280px;
  overflow-y: auto;
}

.filter-radio {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #344054;
  cursor: pointer;
}

.filter-radio input[type="radio"] {
  accent-color: #2d9e64;
  width: 15px;
  height: 15px;
  cursor: pointer;
}

.filter-count {
  margin-left: auto;
  font-size: 12px;
  color: #98a2b3;
}

.apply-btn {
  width: 100%;
  padding: 10px;
  background: #2d9e64;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
  transition: background 0.15s;
  margin-top: 20px;
}

.apply-btn:hover { background: #1f7a4a; }

/* Active Tags */
.active-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-items: center;
}

.active-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  background: #d8f3e3;
  color: #1f7a4a;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 500;
}

.active-tag button {
  background: none;
  border: none;
  cursor: pointer;
  color: #065f46;
  font-size: 14px;
  line-height: 1;
  padding: 0;
}

.clear-all-btn {
  font-size: 12px;
  color: #f04438;
  background: none;
  border: none;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
  text-decoration: underline;
}

/* ─── Content ─────────────────────────────────────────────── */
.catalog-content { min-width: 0; }

.result-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.result-info {
  font-size: 14px;
  color: #344054;
  font-weight: 500;
}

/* Book Grid */
.pub-books-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.pub-book-card {
  background: #fff;
  border: 1px solid #d8f3e3;
  border-radius: 16px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  cursor: pointer;
}

.pub-book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(45,158,100,0.18);
  border-color: #a8e8c4;
}

.pub-book-cover {
  position: relative;
  background: #f0faf4;
  aspect-ratio: 4/3;
  overflow: hidden;
}

/* Hover overlay on cover */
.pub-book-overlay {
  position: absolute;
  inset: 0;
  background: rgba(37, 160, 126, 0.78);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s ease;
  backdrop-filter: blur(2px);
}

.pub-book-card:hover .pub-book-overlay {
  opacity: 1;
}

.pub-book-overlay-text {
  color: white;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.03em;
}

.pub-book-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pub-book-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f0faf4, #d8f3e3);
}

.pub-cat-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  padding: 3px 8px;
  background: rgba(255,255,255,0.92);
  border-radius: 6px;
  font-size: 10px;
  font-weight: 700;
  color: #344054;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.pub-book-info {
  padding: 12px;
}

.pub-book-title {
  font-size: 13px;
  font-weight: 700;
  color: #101828;
  line-height: 1.35;
  margin-bottom: 3px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.pub-book-author {
  font-size: 12px;
  color: #667085;
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pub-book-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
  margin-bottom: 6px;
}

.pub-rack {
  font-size: 11px;
  font-weight: 600;
  color: #344054;
  background: #f2f4f7;
  padding: 2px 7px;
  border-radius: 4px;
}

.pub-avail {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 999px;
}

.pub-avail.avail { background: #dcfce7; color: #15803d; }
.pub-avail.borr  { background: #fee2e2; color: #b91c1c; }

/* Empty State */
.pub-empty {
  text-align: center;
  padding: 80px 24px;
  color: #98a2b3;
}

.pub-empty svg { margin: 0 auto 16px; opacity: 0.5; }

.pub-empty-title {
  font-size: 18px;
  font-weight: 700;
  color: #344054;
  margin-bottom: 8px;
}

.pub-empty-sub {
  font-size: 14px;
  color: #667085;
  margin-bottom: 20px;
}

.pub-empty-btn {
  padding: 9px 20px;
  background: #2d9e64;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
}

/* Pagination */
.pub-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 40px;
  padding-top: 24px;
  border-top: 1px solid #f2f4f7;
}

.page-btn {
  padding: 8px 18px;
  background: white;
  border: 1.5px solid #d0d5dd;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #344054;
  text-decoration: none;
  transition: all 0.15s;
}

.page-btn:hover {
  background: #f9fafb;
  border-color: #1a6b5a;
  color: #2d9e64;
}

.page-info {
  font-size: 14px;
  color: #667085;
}

/* ─── Book Detail Modal ──────────────────────────────────── */
.book-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(10, 30, 20, 0.55);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.book-modal {
  background: #fff;
  border-radius: 24px;
  box-shadow: 0 32px 80px rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 780px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  padding: 36px;
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f2f4f7;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  color: #667085;
  transition: background 0.15s, color 0.15s;
  z-index: 10;
}

.modal-close:hover {
  background: #fee2e2;
  color: #b91c1c;
}

.modal-inner {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

/* Cover column */
.modal-cover-col {
  flex-shrink: 0;
  width: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.modal-cover-wrap {
  width: 180px;
  height: 240px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0,0,0,0.14);
  border: 1px solid #e5e7eb;
}

.modal-cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.modal-cover-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f0faf4, #d8f3e3);
}

.modal-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  width: 100%;
  justify-content: center;
}

.modal-status-badge.status-avail {
  background: #dcfce7;
  color: #15803d;
}

.modal-status-badge.status-borr {
  background: #fee2e2;
  color: #b91c1c;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: currentColor;
  display: inline-block;
}

.modal-stock {
  font-size: 11px;
  color: #667085;
  text-align: center;
}

/* Detail column */
.modal-detail-col {
  flex: 1;
  min-width: 0;
}

.modal-cat-badge {
  display: inline-block;
  padding: 3px 10px;
  background: #d8f3e3;
  color: #1a6b5a;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 10px;
}

.modal-title {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 22px;
  font-weight: 700;
  color: #101828;
  line-height: 1.3;
  margin-bottom: 5px;
}

.modal-author {
  font-size: 14px;
  color: #667085;
  margin-bottom: 18px;
  font-style: italic;
}

.modal-meta-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 20px;
  margin-bottom: 20px;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.meta-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #98a2b3;
}

.meta-value {
  font-size: 13px;
  font-weight: 600;
  color: #344054;
}

.modal-divider {
  height: 1px;
  background: #f2f4f7;
  margin-bottom: 18px;
}

.modal-synopsis-section { }

.modal-synopsis-title {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #344054;
  margin-bottom: 10px;
}

.modal-synopsis-text {
  font-size: 14px;
  color: #475467;
  line-height: 1.75;
  white-space: pre-line;
}

.modal-synopsis-empty {
  font-size: 14px;
  color: #667085;
  font-style: italic;
  line-height: 1.7;
}

/* Transition */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-active .book-modal,
.modal-leave-active .book-modal {
  transition: transform 0.25s ease, opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .book-modal,
.modal-leave-to .book-modal {
  transform: scale(0.95) translateY(16px);
  opacity: 0;
}
</style>
