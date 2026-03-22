<template>
  <div v-if="active" class="scanner-container">
    <div class="scanner-header">
      <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ type === 'qr' ? 'Scan QR Anggota' : 'Scan Barcode Buku' }}
      </div>
      <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="scanner-body">
      <div :id="readerId" class="scanner-reader"></div>
      <div v-if="error" class="scanner-error">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
          <path d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.5-12.998c.866-1.5 3.032-1.5 3.898 0z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        {{ error }}
      </div>
      <p class="scanner-hint">
        {{ type === 'qr' ? 'Arahkan kamera ke QR Code anggota' : 'Arahkan kamera ke barcode buku' }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount, nextTick } from 'vue'
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode'

const props = defineProps({
  type:   { type: String, default: 'qr' },   // 'qr' | 'barcode'
  active: { type: Boolean, default: false },
})

const emit = defineEmits(['scanned', 'close'])

const readerId = `scanner-${Math.random().toString(36).slice(2, 8)}`
const error = ref('')
let scanner = null

// Format yang didukung per tipe scan
const qrFormats = [
  Html5QrcodeSupportedFormats.QR_CODE,
]

const barcodeFormats = [
  Html5QrcodeSupportedFormats.CODE_128,
  Html5QrcodeSupportedFormats.CODE_39,
  Html5QrcodeSupportedFormats.EAN_13,
  Html5QrcodeSupportedFormats.EAN_8,
  Html5QrcodeSupportedFormats.UPC_A,
  Html5QrcodeSupportedFormats.UPC_E,
  Html5QrcodeSupportedFormats.QR_CODE,
]

async function startScanner() {
  error.value = ''

  await nextTick()

  // Pastikan element sudah ada di DOM
  const el = document.getElementById(readerId)
  if (!el) return

  try {
    const formats = props.type === 'qr' ? qrFormats : barcodeFormats

    scanner = new Html5Qrcode(readerId, {
      formatsToSupport: formats,
      verbose: false,
    })

    const config = {
      fps: props.type === 'qr' ? 10 : 15,
      qrbox: props.type === 'qr'
        ? { width: 220, height: 220 }
        : { width: 320, height: 120 },
      aspectRatio: props.type === 'barcode' ? 2.0 : 1.0,
    }

    await scanner.start(
      { facingMode: 'environment' },
      config,
      (decodedText) => {
        emit('scanned', decodedText)
        stopScanner()
      },
      () => {} // ignore scan failures
    )
  } catch (e) {
    console.error('Scanner error:', e)
    // Fallback: coba kamera depan jika belakang tidak tersedia
    try {
      const formats = props.type === 'qr' ? qrFormats : barcodeFormats
      scanner = new Html5Qrcode(readerId, {
        formatsToSupport: formats,
        verbose: false,
      })

      await scanner.start(
        { facingMode: 'user' },
        {
          fps: 10,
          qrbox: props.type === 'qr'
            ? { width: 200, height: 200 }
            : { width: 280, height: 100 },
        },
        (decodedText) => {
          emit('scanned', decodedText)
          stopScanner()
        },
        () => {}
      )
    } catch (e2) {
      console.error('Scanner fallback error:', e2)
      error.value = 'Tidak dapat mengakses kamera. Pastikan kamera diizinkan di browser.'
    }
  }
}

async function stopScanner() {
  if (scanner) {
    try {
      const state = scanner.getState()
      // State: 1=NOT_STARTED, 2=SCANNING, 3=PAUSED
      if (state === 2) {
        await scanner.stop()
      }
    } catch (e) {
      // ignore
    }
    try {
      scanner.clear()
    } catch (e) {
      // ignore
    }
    scanner = null
  }
}

watch(() => props.active, async (val) => {
  if (val) {
    await startScanner()
  } else {
    await stopScanner()
  }
})

onBeforeUnmount(() => {
  stopScanner()
})
</script>

<style scoped>
.scanner-container {
  margin-top: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  background: #f8fafc;
}

.scanner-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.scanner-body {
  padding: 12px;
}

.scanner-reader {
  border-radius: 8px;
  overflow: hidden;
}

/* Override html5-qrcode styles */
.scanner-reader :deep(video) {
  border-radius: 8px;
}

.scanner-reader :deep(#qr-shaded-region) {
  border-color: rgba(99, 102, 241, 0.5) !important;
}

.scanner-error {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 8px;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12px;
  color: #dc2626;
  background: #fef2f2;
  border: 1px solid #fecaca;
}

.scanner-hint {
  margin-top: 8px;
  font-size: 12px;
  color: #94a3b8;
  text-align: center;
}
</style>
