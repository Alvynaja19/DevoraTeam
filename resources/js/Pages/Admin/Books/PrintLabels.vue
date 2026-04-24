<template>
  <AdminLayout title="Cetak Label Barcode">
    <template #topbar-actions>
      <div class="flex items-center gap-3">
        <button @click="printAll" class="btn btn-primary gap-2">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.75 19.5m10.56-5.671.096-.001M17.25 19.5l-.441-5.672M3 8.25V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25M3 8.25h18M3 8.25a2.25 2.25 0 0 0-2.25 2.25v8.25A2.25 2.25 0 0 0 3 20.25h18a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 21 8.25" />
          </svg>
          Print Semua ({{ copies.length }} label)
        </button>
      </div>
    </template>

    <!-- Search & Info Bar -->
    <div class="card p-4 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
      <div class="flex items-center gap-3 text-sm text-slate-600">
        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/>
          </svg>
        </div>
        <span>Total <strong>{{ copies.length }}</strong> label siap cetak</span>
      </div>
      <div class="flex gap-3 w-full sm:w-auto">
        <div class="relative flex-1 sm:w-72">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="searchInput" @keyup.enter="doSearch" type="text"
            placeholder="Cari judul atau penulis..."
            class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-400 outline-none" />
        </div>
        <button @click="doSearch" class="btn btn-outline text-sm">Cari</button>
        <button v-if="search" @click="clearSearch" class="btn btn-secondary text-sm">Reset</button>
      </div>
    </div>

    <!-- Panduan Print -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex gap-3 items-start">
      <svg class="flex-shrink-0 mt-0.5" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
      </svg>
      <div class="text-sm text-amber-800">
        <strong>Cara Print Label:</strong>
        Klik tombol <strong>"Print Semua"</strong> di pojok kanan atas → browser akan buka dialog print →
        pilih printer Anda → klik Print. Gunakan kertas <strong>stiker A4</strong> untuk hasil terbaik.
        Setelah dicetak, gunting dan tempel di punggung buku fisik.
      </div>
    </div>

    <!-- Grid Label -->
    <div v-if="copies.length > 0" id="label-print-area" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
      <div v-for="copy in copies" :key="copy.id"
        class="label-card bg-white border border-slate-200 rounded-xl p-3 flex flex-col items-center gap-1 shadow-sm hover:shadow-md transition-shadow">
        <!-- Kode Buku -->
        <div class="text-[10px] font-bold text-indigo-600 tracking-wide uppercase">{{ copy.copy_code }}</div>
        <!-- Judul -->
        <div class="text-[11px] font-semibold text-slate-700 text-center line-clamp-2 leading-tight" :title="copy.book?.title">
          {{ copy.book?.title }}
        </div>
        <!-- Barcode -->
        <div class="bg-white rounded py-1 px-0.5 w-full flex justify-center">
          <svg class="barcode-svg" :data-copy-id="copy.id" :data-barcode="copy.barcode"
            style="width:100%; max-height:50px;"></svg>
        </div>
        <!-- Tombol Print Individual -->
        <button @click="printOne(copy)" class="mt-1 text-[10px] text-indigo-500 hover:text-indigo-700 font-medium flex items-center gap-1 no-print">
          <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.75 19.5m10.56-5.671.096-.001M17.25 19.5l-.441-5.672M3 8.25V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25M3 8.25h18M3 8.25a2.25 2.25 0 0 0-2.25 2.25v8.25A2.25 2.25 0 0 0 3 20.25h18a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 21 8.25"/>
          </svg>
          Print Label Ini
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center py-20 text-slate-400">
      <svg class="mx-auto mb-3 text-slate-300" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/>
      </svg>
      <p class="font-medium">Tidak ada data eksemplar ditemukan</p>
      <p class="text-sm mt-1">Coba kata kunci pencarian lain</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/TailAdminLayout.vue'
import JsBarcode from 'jsbarcode'

const props = defineProps({
  copies: { type: Array, default: () => [] },
  search: { type: String, default: '' },
  total:  { type: Number, default: 0 },
})

const searchInput = ref(props.search || '')

// Render semua barcode setelah DOM siap
onMounted(async () => {
  await nextTick()
  renderBarcodes()
})

function renderBarcodes() {
  document.querySelectorAll('.barcode-svg').forEach(el => {
    if (!el.dataset.barcode) return
    try {
      JsBarcode(el, el.dataset.barcode, {
        format: 'CODE128',
        height: 38,
        displayValue: true,
        fontSize: 9,
        margin: 2,
        background: '#ffffff',
      })
    } catch (e) {
      console.warn('Barcode error:', el.dataset.barcode, e)
    }
  })
}

function doSearch() {
  router.get(route('books.print-labels'), { search: searchInput.value }, {
    preserveState: true,
    onSuccess: async () => {
      await nextTick()
      renderBarcodes()
    }
  })
}

function clearSearch() {
  searchInput.value = ''
  router.get(route('books.print-labels'), {}, {
    onSuccess: async () => {
      await nextTick()
      renderBarcodes()
    }
  })
}

// Print semua label
function printAll() {
  window.print()
}

// Print satu label saja
function printOne(copy) {
  // Buat konten SVG barcode untuk label yang dipilih
  const svgEl = document.querySelector(`[data-copy-id="${copy.id}"].barcode-svg`)
  const svgContent = svgEl ? svgEl.outerHTML : ''

  const w = window.open('', '_blank', 'width=400,height=300')
  w.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Label: ${copy.copy_code}</title>
      <style>
        @page { size: 60mm 40mm; margin: 0; }
        body { margin: 0; padding: 6px; font-family: Arial, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 60mm; min-height: 40mm; box-sizing: border-box; }
        .code  { font-size: 9px; font-weight: bold; color: #4f46e5; letter-spacing: 0.5px; text-transform: uppercase; }
        .title { font-size: 8px; font-weight: 600; color: #1e293b; text-align: center; margin: 2px 0; max-width: 55mm; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        svg    { width: 55mm; height: auto; }
      </style>
    </head>
    <body>
      <div class="code">${copy.copy_code}</div>
      <div class="title">${copy.book?.title || ''}</div>
      ${svgContent}
      <script>window.onload = function() { window.print(); window.close(); }<\/script>
    </body>
    </html>
  `)
  w.document.close()
}
</script>

<style>
/* ===== PRINT STYLES ===== */
@media print {
  /* Sembunyikan semua elemen layout */
  body > *:not(#app) { display: none !important; }

  /* Sembunyikan sidebar, navbar, topbar */
  nav, aside, header, footer,
  [class*="sidebar"], [class*="navbar"], [class*="topbar"],
  .no-print { display: none !important; }

  /* Print area - grid label */
  #label-print-area {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 4mm !important;
    padding: 5mm !important;
  }

  .label-card {
    border: 1px solid #ccc !important;
    border-radius: 4mm !important;
    padding: 3mm !important;
    break-inside: avoid !important;
    page-break-inside: avoid !important;
  }

  @page {
    size: A4;
    margin: 10mm;
  }
}
</style>
