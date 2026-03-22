<template>
  <PublicLayout>
    <!-- ── HERO ── -->
    <section class="hero">
      <div class="hero-content">
        <div class="hero-tag">Perpustakaan Digital Sekolah</div>
        <h1>Temukan Dunia<br>dalam Setiap <em>Halaman</em></h1>
        <p class="hero-desc">
          Akses koleksi buku perpustakaan sekolah secara digital.
          Dari literatur klasik hingga referensi terbaru, semua tersedia untukmu.
        </p>
        <div class="hero-actions">
          <Link :href="route('catalog')" class="btn-primary">Jelajahi Katalog</Link>
          <Link :href="route('register')" class="btn-ghost">Daftar Akun →</Link>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-num">{{ formatNum(stats.total_books) }}+</span>
            <span class="stat-label">Judul Buku</span>
          </div>
          <div class="stat-item">
            <span class="stat-num">{{ formatNum(stats.total_members) }}</span>
            <span class="stat-label">Anggota Aktif</span>
          </div>
          <div class="stat-item">
            <span class="stat-num">{{ stats.total_categories }}</span>
            <span class="stat-label">Kategori</span>
          </div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="book-stack">
          <div v-for="(b, i) in heroBooks" :key="i" class="book-card" :class="'bc' + ((i % 6) + 1)">
            <div class="book-card-inner">
              <div class="book-spine"></div>
              <span class="book-title-mini">{{ b }}</span>
            </div>
          </div>
        </div>
        <div class="floating-badge">
          <span class="badge-icon">📚</span>
          <div class="badge-text">
            <span class="badge-num">{{ formatNum(stats.total_loans) }} Dipinjam</span>
            <span class="badge-sub">Sedang beredar saat ini</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ── FEATURES ── -->
    <section class="features">
      <div class="section-header reveal">
        <span class="section-tag">Layanan Kami</span>
        <h2 class="section-title">Kenapa Memilih <em>SMA NEGERI 4 JEMBER</em></h2>
        <p class="section-sub">Perpustakaan yang nyaman, modern, dan dirancang untuk mendukung kegiatan belajarmu.</p>
      </div>
      <div class="features-grid">
        <div class="feat-card reveal">
          <div class="feat-icon">🔍</div>
          <h3 class="feat-title">Pencarian Cepat</h3>
          <p class="feat-desc">Temukan buku yang kamu butuhkan dalam hitungan detik. Cari berdasarkan judul, penulis, atau kategori.</p>
        </div>
        <div class="feat-card reveal">
          <div class="feat-icon">📱</div>
          <h3 class="feat-title">QR Code Anggota</h3>
          <p class="feat-desc">Tunjukkan QR code profilmu ke petugas untuk meminjam buku. Cepat, tanpa antri, tanpa ribet.</p>
        </div>
        <div class="feat-card reveal">
          <div class="feat-icon">🗂️</div>
          <h3 class="feat-title">Katalog Lengkap</h3>
          <p class="feat-desc">Koleksi buku tersusun rapi dalam berbagai kategori — dari fiksi, sains, hingga jurnal dan referensi.</p>
        </div>
        <div class="feat-card reveal">
          <div class="feat-icon">📊</div>
          <h3 class="feat-title">Riwayat Peminjaman</h3>
          <p class="feat-desc">Pantau buku yang sedang dipinjam, tanggal jatuh tempo, dan riwayat peminjaman sebelumnya.</p>
        </div>
        <div class="feat-card reveal">
          <div class="feat-icon">🔔</div>
          <h3 class="feat-title">Notifikasi Otomatis</h3>
          <p class="feat-desc">Tidak perlu khawatir lupa mengembalikan. Sistem kami akan mengirim pengingat sebelum jatuh tempo.</p>
        </div>
        <div class="feat-card reveal">
          <div class="feat-icon">📖</div>
          <h3 class="feat-title">Koleksi Terbaik</h3>
          <p class="feat-desc">Kami secara rutin menambahkan koleksi baru berdasarkan rekomendasi guru dan minat siswa.</p>
        </div>
      </div>
    </section>

    <!-- ── COLLECTION / KATEGORI ── -->
    <section class="collection">
      <div class="collection-inner">
        <div class="section-header reveal">
          <span class="section-tag">Koleksi Unggulan</span>
          <h2 class="section-title">Jelajahi <em>Kategori</em><br>Favorit Kamu</h2>
        </div>
        <div class="collection-grid">
          <Link v-for="(cat, i) in categories.slice(0, 8)" :key="cat.id"
            :href="route('catalog', { category: cat.id })"
            class="col-card reveal">
            <div class="col-cover">
              <div class="col-cover-bg" :style="coverGradient(i)"></div>
              <span class="col-cover-icon">{{ getCatIcon(cat.name) }}</span>
            </div>
            <div class="col-info">
              <div class="col-genre">{{ cat.name }}</div>
              <div class="col-count">{{ cat.books_count }} judul tersedia</div>
            </div>
          </Link>
        </div>
      </div>
    </section>

    <!-- ── CTA STRIP ── -->
    <div class="cta-strip reveal">
      <div class="cta-text">
        <h2>Mulai Membaca Hari Ini</h2>
        <p>Bergabung dengan {{ formatNum(stats.total_members) }} anggota aktif dan akses {{ formatNum(stats.total_books) }}+ koleksi buku di perpustakaan kami.</p>
      </div>
      <Link :href="route('register')" class="btn-white">Bergabung Sekarang →</Link>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  stats: Object,
  popularBooks: Array,
  categories: Array,
  settings: Object,
})

// Hero book labels from top categories
const heroBooks = props.categories?.slice(0, 6).map(c => c.name) || [
  'Sastra', 'Sains', 'Sejarah', 'Teknologi', 'Fiksi', 'Sosial'
]

function formatNum(n) {
  if (!n) return '0'
  if (n >= 1000) return (n / 1000).toFixed(1).replace('.0', '') + 'rb'
  return String(n)
}

const gradients = [
  'linear-gradient(145deg, #d8f3e3, #7dd3a8)',
  'linear-gradient(145deg, #b2e6c7, #4aba82)',
  'linear-gradient(145deg, #7dd3a8, #2d9e64)',
  'linear-gradient(145deg, #f0faf4, #b2e6c7)',
  'linear-gradient(145deg, #d4edda, #7dd3a8)',
  'linear-gradient(145deg, #4aba82, #2d9e64)',
  'linear-gradient(145deg, #e8f7ee, #7dd3a8)',
  'linear-gradient(145deg, #2d9e64, #1f7a4a)',
]

function coverGradient(i) {
  return { background: gradients[i % gradients.length] }
}

const catIcons = {
  'Sains': '🔬', 'Teknologi': '💻', 'Matematika': '📐',
  'Sejarah': '🏛️', 'Geografi': '🌍', 'Bahasa': '📖',
  'Sastra': '✍️', 'Seni': '🎨', 'Olahraga': '⚽',
  'Agama': '📿', 'Ekonomi': '💰', 'Biologi': '🧬',
  'Fisika': '⚛️', 'Kimia': '🧪', 'Sosial': '👥',
  'Fiksi': '🌟', 'Novel': '📚', 'Ensiklopedia': '📕',
}

function getCatIcon(name) {
  for (const [key, val] of Object.entries(catIcons)) {
    if (name?.toLowerCase().includes(key.toLowerCase())) return val
  }
  return '📚'
}

// Scroll reveal
let observer = null
onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add('visible'), i * 80)
        observer.unobserve(entry.target)
      }
    })
  }, { threshold: 0.12 })

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el))
})

onUnmounted(() => { observer?.disconnect() })
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap');

:root {
  --green-50:  #f0faf4;
  --green-100: #d8f3e3;
  --green-200: #b2e6c7;
  --green-300: #7dd3a8;
  --green-400: #4aba82;
  --green-500: #2d9e64;
  --green-600: #1f7a4a;
  --cream:     #faf8f3;
  --warm:      #f3ede0;
  --ink:       #1a1f1c;
  --ink-soft:  #4a5250;
  --ink-muted: #8a9490;
  --serif:     'Cormorant Garamond', Georgia, serif;
  --sans:      'Outfit', 'DM Sans', sans-serif;
}

/* ── HERO ── */
.hero {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  padding: 8rem 5vw 5rem;
  gap: 4rem;
  position: relative;
  overflow: hidden;
  background: var(--cream);
}

.hero::before {
  content: '';
  position: absolute;
  top: -20%;
  right: -10%;
  width: 55vw;
  height: 55vw;
  background: radial-gradient(circle, var(--green-100) 0%, transparent 70%);
  pointer-events: none;
}

.hero-tag {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .35rem 1rem;
  border-radius: 2rem;
  background: var(--green-100);
  color: var(--green-600);
  font-size: .8rem;
  font-weight: 500;
  letter-spacing: .05em;
  text-transform: uppercase;
  margin-bottom: 1.6rem;
  animation: fadeUp .7s .2s ease both;
}

.hero-tag::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--green-500);
}

.hero h1 {
  font-family: var(--serif);
  font-size: clamp(2.8rem, 5vw, 4.5rem);
  font-weight: 400;
  line-height: 1.12;
  letter-spacing: -.01em;
  margin-bottom: 1.4rem;
  color: var(--ink);
  animation: fadeUp .7s .35s ease both;
}

.hero h1 em {
  font-style: italic;
  color: var(--green-500);
}

.hero-desc {
  font-size: 1.05rem;
  color: var(--ink-soft);
  line-height: 1.75;
  max-width: 42ch;
  margin-bottom: 2.4rem;
  animation: fadeUp .7s .5s ease both;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  flex-wrap: wrap;
  animation: fadeUp .7s .65s ease both;
}

.btn-primary {
  padding: .8rem 2rem;
  border-radius: 2rem;
  background: var(--green-500);
  color: #fff;
  font-size: .95rem;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  transition: background .25s, transform .2s, box-shadow .2s;
  box-shadow: 0 4px 20px rgba(45, 158, 100, .25);
}

.btn-primary:hover {
  background: var(--green-600);
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(45, 158, 100, .35);
}

.btn-ghost {
  padding: .8rem 2rem;
  border-radius: 2rem;
  border: 1.5px solid var(--green-200);
  color: var(--ink-soft);
  font-size: .95rem;
  font-weight: 400;
  text-decoration: none;
  display: inline-block;
  transition: border-color .25s, color .25s, transform .2s;
}

.btn-ghost:hover {
  border-color: var(--green-500);
  color: var(--green-600);
  transform: translateY(-2px);
}

/* Hero stats */
.hero-stats {
  display: flex;
  gap: 2.5rem;
  margin-top: 3rem;
  animation: fadeUp .7s .8s ease both;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: .2rem;
}

.stat-num {
  font-family: var(--serif);
  font-size: 2rem;
  font-weight: 500;
  color: var(--ink);
  line-height: 1;
}

.stat-label {
  font-size: .8rem;
  color: var(--ink-muted);
  letter-spacing: .04em;
}

/* Hero visual */
.hero-visual {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 1s .4s ease both;
}

.book-stack {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  transform: perspective(900px) rotateY(-6deg) rotateX(4deg);
}

.book-card {
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 3/4;
  transition: transform .4s, box-shadow .4s;
  cursor: pointer;
}

.book-card:hover {
  transform: translateY(-10px) scale(1.04);
  box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
}

.book-card:nth-child(2) { margin-top: 1.5rem; }
.book-card:nth-child(3) { margin-top: -.5rem; }

.book-card-inner {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1rem;
  position: relative;
  overflow: hidden;
}

.book-card-inner::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, .08);
}

.book-spine {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 8px;
  background: rgba(0, 0, 0, .15);
}

.bc1 { background: linear-gradient(145deg, #b2e6c7, #4aba82); }
.bc2 { background: linear-gradient(145deg, #d4edda, #7dd3a8); }
.bc3 { background: linear-gradient(145deg, #2d9e64, #1f7a4a); }
.bc4 { background: linear-gradient(145deg, #f0faf4, #b2e6c7); }
.bc5 { background: linear-gradient(145deg, #4aba82, #2d9e64); }
.bc6 { background: linear-gradient(145deg, #e8f7ee, #7dd3a8); }

.book-title-mini {
  font-family: var(--serif);
  font-size: .85rem;
  font-weight: 500;
  color: rgba(255, 255, 255, .9);
  line-height: 1.3;
  position: relative;
  z-index: 1;
}

.floating-badge {
  position: absolute;
  bottom: 2rem;
  right: -1rem;
  background: #fff;
  border-radius: 14px;
  padding: .9rem 1.2rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, .12);
  display: flex;
  align-items: center;
  gap: .8rem;
  animation: float 3s ease-in-out infinite;
}

.badge-icon { font-size: 1.6rem; }

.badge-text { display: flex; flex-direction: column; }

.badge-num {
  font-family: var(--serif);
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--green-600);
}

.badge-sub {
  font-size: .72rem;
  color: var(--ink-muted);
}

/* ── FEATURES ── */
.features {
  padding: 7rem 5vw;
  background: var(--green-50);
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-tag {
  display: inline-block;
  font-size: .78rem;
  font-weight: 500;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--green-600);
  margin-bottom: .9rem;
}

.section-title {
  font-family: var(--serif);
  font-size: clamp(2rem, 3.5vw, 2.8rem);
  font-weight: 400;
  line-height: 1.2;
  color: var(--ink);
}

.section-title em {
  font-style: italic;
  color: var(--green-500);
}

.section-sub {
  margin-top: .8rem;
  font-size: 1rem;
  color: var(--ink-soft);
  max-width: 48ch;
  margin-inline: auto;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.8rem;
  max-width: 1100px;
  margin: 0 auto;
}

.feat-card {
  background: #fff;
  border-radius: 18px;
  padding: 2.2rem 2rem;
  border: 1px solid var(--green-100);
  transition: transform .3s, box-shadow .3s, border-color .3s;
  position: relative;
  overflow: hidden;
}

.feat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(45, 158, 100, .12);
  border-color: var(--green-200);
}

.feat-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: var(--green-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-bottom: 1.4rem;
  transition: background .3s;
}

.feat-card:hover .feat-icon { background: var(--green-200); }

.feat-title {
  font-family: var(--serif);
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: .6rem;
  color: var(--ink);
}

.feat-desc {
  font-size: .9rem;
  color: var(--ink-soft);
  line-height: 1.7;
}

.feat-card::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--green-300), var(--green-500));
  transform: scaleX(0);
  transform-origin: left;
  transition: transform .35s;
}

.feat-card:hover::after { transform: scaleX(1); }

/* ── COLLECTION ── */
.collection {
  padding: 7rem 5vw;
  background: var(--cream);
}

.collection-inner {
  max-width: 1100px;
  margin: 0 auto;
}

.collection-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.4rem;
  margin-top: 3.5rem;
}

.col-card {
  border-radius: 16px;
  overflow: hidden;
  background: var(--green-50);
  border: 1px solid var(--green-100);
  transition: transform .3s, box-shadow .3s;
  cursor: pointer;
  text-decoration: none;
  display: block;
}

.col-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 45px rgba(45, 158, 100, .15);
}

.col-cover {
  height: 140px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.col-cover-bg {
  position: absolute;
  inset: 0;
}

.col-cover-icon {
  font-size: 2.8rem;
  position: relative;
  z-index: 1;
}

.col-info {
  padding: 1.2rem 1.3rem 1.4rem;
}

.col-genre {
  font-family: var(--serif);
  font-size: 1.05rem;
  font-weight: 500;
  line-height: 1.3;
  color: var(--ink);
  margin-bottom: .4rem;
}

.col-count {
  font-size: .8rem;
  color: var(--ink-muted);
}

/* ── CTA STRIP ── */
.cta-strip {
  margin: 0 5vw 7rem;
  background: linear-gradient(135deg, var(--green-500) 0%, var(--green-600) 100%);
  border-radius: 24px;
  padding: 4rem 5%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  flex-wrap: wrap;
  position: relative;
  overflow: hidden;
}

.cta-strip::before {
  content: '';
  position: absolute;
  top: -30%;
  right: -10%;
  width: 40%;
  height: 200%;
  background: rgba(255, 255, 255, .06);
  transform: rotate(15deg);
  pointer-events: none;
}

.cta-text {
  position: relative;
  z-index: 1;
}

.cta-text h2 {
  font-family: var(--serif);
  font-size: clamp(1.6rem, 3vw, 2.4rem);
  font-weight: 400;
  color: #fff;
  margin-bottom: .6rem;
}

.cta-text p {
  font-size: .95rem;
  color: rgba(255, 255, 255, .8);
  max-width: 48ch;
}

.btn-white {
  position: relative;
  z-index: 1;
  padding: .9rem 2.2rem;
  border-radius: 2rem;
  background: #fff;
  color: var(--green-600);
  font-size: .95rem;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  white-space: nowrap;
  transition: transform .2s, box-shadow .2s;
  box-shadow: 0 4px 20px rgba(0, 0, 0, .2);
}

.btn-white:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
}

/* ── ANIMATIONS ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50%      { transform: translateY(-10px); }
}

/* Scroll reveal */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity .7s ease, transform .7s ease;
}

.reveal.visible {
  opacity: 1;
  transform: translateY(0);
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
  .hero { grid-template-columns: 1fr; padding-top: 7rem; }
  .hero-visual { display: none; }
  .features-grid { grid-template-columns: 1fr 1fr; }
  .collection-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 600px) {
  .features-grid,
  .collection-grid { grid-template-columns: 1fr; }
  .cta-strip { text-align: center; justify-content: center; }
  .hero-stats { gap: 1.5rem; flex-wrap: wrap; }
}
</style>
