@extends('layouts.public')

@section('content')

<!-- HEADER / HERO SECTION -->
<section class="max-w-[1400px] mx-auto px-4 md:px-6 pt-8 pb-16">
    <div class="bg-gradient-to-br from-[#ece8d9] to-[#d8d1bc] rounded-[2.5rem] p-8 md:p-16 flex flex-col md:flex-row items-center gap-12 relative overflow-hidden">
        
        <!-- Text Content -->
        <div class="w-full md:w-1/2 z-10">
            <div class="inline-block px-4 py-1.5 bg-white/60 backdrop-blur-sm rounded-full text-xs font-bold text-green-800 tracking-wider mb-8">
                LITERASI UNTUK NEGERI
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 leading-[1.1] mb-6 font-serif">
                Selamat Datang di <br>
                Perpustakaan <br>
                <span class="text-green-700">SMAN 4 Jember</span>
            </h1>
            <p class="text-gray-700 text-lg sm:text-xl max-w-lg mb-10 leading-relaxed">
                Temukan jendela dunia melalui koleksi literatur terlengkap dan layanan modern untuk mendukung prestasi akademik Anda.
            </p>
            
            <div class="flex flex-wrap items-center gap-4">
                <a href="#" class="px-8 py-3.5 bg-green-700 text-white font-semibold rounded-full hover:bg-green-800 transition shadow-lg shadow-green-700/30 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari Buku
                </a>
                <a href="#" class="px-8 py-3.5 bg-white text-green-800 font-semibold rounded-full hover:bg-gray-50 transition shadow-sm">
                    Lihat Koleksi
                </a>
            </div>
        </div>

        <!-- Illustration -->
        <div class="w-full md:w-1/2 relative h-[300px] md:h-[450px] z-10 flex justify-end">
            <!-- Decorative Library Illustration Wrapper -->
            <div class="w-full max-w-md bg-[#84A588] rounded-2xl overflow-hidden shadow-2xl relative border-8 border-white/50">
                <!-- Simple CSS Illustration of Library -->
                <div class="absolute inset-0 bg-gradient-to-b from-[#A5C1A9] to-[#719275]"></div>
                <!-- Ceiling lights -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 flex flex-col gap-6 items-center w-full perspective-1000">
                    <div class="w-1/2 h-2 bg-white/80 rounded-full shadow-[0_0_15px_rgba(255,255,255,0.8)] filter blur-[1px]"></div>
                    <div class="w-1/3 h-2 bg-white/80 rounded-full shadow-[0_0_15px_rgba(255,255,255,0.8)] filter blur-[1px]"></div>
                    <div class="w-1/4 h-2 bg-white/80 rounded-full shadow-[0_0_15px_rgba(255,255,255,0.8)] filter blur-[1px]"></div>
                </div>
                <!-- Left shelf -->
                <div class="absolute left-0 top-0 bottom-0 w-[35%] bg-[#DAA520]/80 border-r-8 border-[#B8860B] flex flex-col justify-around py-4 transform origin-left perspective-1000 skew-y-6">
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                </div>
                <!-- Right shelf -->
                <div class="absolute right-0 top-0 bottom-0 w-[35%] bg-[#DAA520]/80 border-l-8 border-[#B8860B] flex flex-col justify-around py-4 transform origin-right perspective-1000 -skew-y-6">
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                    <div class="w-full border-b-4 border-[#8B6508] shadow-sm mb-4"></div>
                </div>
                <!-- Floor -->
                <div class="absolute bottom-0 left-0 right-0 h-1/4 bg-green-900/40 perspective-1000"></div>
            </div>
        </div>

    </div>
</section>

<!-- STATS CARDS -->
<section class="max-w-[1200px] mx-auto px-6 -mt-24 md:-mt-28 relative z-20 mb-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Total Koleksi -->
        <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 flex items-center gap-5 border border-gray-50">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-700 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Total Koleksi</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-900 group-hover:text-green-700 transition">{{ number_format($totalBooks ?? 0, 0, ',', '.') }}</h3>
                    <span class="text-sm text-green-600 font-medium">Buku</span>
                </div>
            </div>
        </div>

        <!-- Anggota Aktif -->
        <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 flex items-center gap-5 border border-gray-50">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-700 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Anggota Aktif</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalMembers ?? 0, 0, ',', '.') }}</h3>
                    <span class="text-sm text-green-600 font-medium">Siswa</span>
                </div>
            </div>
        </div>

        <!-- Buku Dipinjam -->
        <div class="bg-white rounded-2xl p-6 shadow-xl shadow-gray-200/50 flex items-center gap-5 border border-gray-50">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-700 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Buku Dipinjam</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($activeBorrowings ?? 0, 0, ',', '.') }}</h3>
                    <span class="text-sm text-green-600 font-medium">Sirkulasi</span>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- EKSPLORASI KATEGORI -->
<section class="max-w-[1200px] mx-auto px-6 py-16 text-center">
    <h2 class="text-3xl font-bold text-gray-900 mb-4 font-serif">Eksplorasi Kategori</h2>
    <p class="text-gray-500 max-w-2xl mx-auto mb-12">Temukan berbagai genre dan jenis koleksi sesuai minat dan kebutuhan riset Anda.</p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        
        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Fiksi</h3>
        </a>

        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Non-Fiksi</h3>
        </a>

        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Referensi</h3>
        </a>

        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Majalah</h3>
        </a>

        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Karya Ilmiah</h3>
        </a>

        <a href="#" class="group block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 mx-auto bg-green-50 text-green-700 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-800">Digital</h3>
        </a>

    </div>
</section>

<!-- KOLEKSI UNGGULAN -->
<section class="max-w-[1200px] mx-auto px-6 py-16">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-serif">Koleksi Unggulan</h2>
            <p class="text-gray-500">Buku-buku paling populer minggu ini.</p>
        </div>
        <a href="#" class="hidden md:flex items-center gap-1 text-green-700 font-semibold hover:text-green-800 hover:gap-2 transition-all">
            Lihat Semua 
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <!-- Book Grid - Uses database data or fallbacks -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @forelse($books as $book)
            <div class="group bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                <!-- Book Cover (Mockup aspect ratio) -->
                <div class="aspect-[3/4] bg-gray-100 rounded-xl mb-4 overflow-hidden relative shadow-inner">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" class="w-full h-full object-cover">
                    @else
                        <!-- Placeholder cover if no image -->
                        <div class="absolute inset-0 bg-green-800 flex flex-col items-center justify-center p-4 text-center">
                            <h4 class="text-white font-serif font-bold text-sm leading-tight mb-2">{{ $book->judul }}</h4>
                            <div class="w-8 h-0.5 bg-green-500 mb-2"></div>
                            <p class="text-green-100 text-xs">{{ $book->pengarang }}</p>
                        </div>
                    @endif
                </div>
                <div class="flex flex-col flex-1">
                    <h3 class="font-bold text-gray-900 line-clamp-2 mb-1 leading-tight group-hover:text-green-700 transition">{{ $book->judul }}</h3>
                    <p class="text-gray-500 text-xs mb-3 truncate">{{ $book->pengarang }}</p>
                    
                    <div class="mt-auto">
                        <a href="#" class="inline-flex items-center justify-center w-full py-2 bg-green-50 text-green-700 text-sm font-semibold rounded-lg group-hover:bg-green-700 group-hover:text-white transition-colors">
                            Detail Buku
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-10 text-gray-500">
                Belum ada data buku unggulan.
            </div>
        @endforelse
    </div>
    
    <div class="mt-8 text-center md:hidden">
        <a href="#" class="inline-flex items-center gap-2 text-green-700 font-semibold px-6 py-2 border border-green-200 rounded-full">
            Lihat Semua Koleksi
        </a>
    </div>
</section>

<!-- CARA MEMINJAM BUKU -->
<section class="max-w-[1200px] mx-auto px-6 py-20">
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-4 font-serif">Cara Meminjam Buku</h2>
        <p class="text-gray-500 max-w-2xl mx-auto">Alur peminjaman buku yang mudah dan terintegrasi secara digital.</p>
    </div>

    <!-- Steps -->
    <div class="grid md:grid-cols-3 gap-10 md:gap-6 relative">
        <!-- Connecting Line for Desktop -->
        <div class="hidden md:block absolute top-8 left-[15%] right-[15%] h-0.5 bg-green-100 -z-10"></div>

        <!-- Step 1 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 text-center relative z-10 hover:shadow-lg transition">
            <div class="w-12 h-12 bg-green-200 text-green-800 rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-6 shadow-sm border-4 border-white">
                1
            </div>
            <div class="w-16 h-16 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h3 class="font-bold text-xl text-gray-900 mb-3">Daftar Akun</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Registrasi sebagai anggota melalui portal digital perpustakaan atau di loket fisik.</p>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 text-center relative z-10 hover:shadow-lg transition">
            <div class="w-12 h-12 bg-green-200 text-green-800 rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-6 shadow-sm border-4 border-white">
                2
            </div>
            <div class="w-16 h-16 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="font-bold text-xl text-gray-900 mb-3">Cari Buku</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Gunakan fitur katalog online untuk mencari buku yang ingin dipinjam dan cek ketersediaannya.</p>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 text-center relative z-10 hover:shadow-lg transition">
            <div class="w-12 h-12 bg-green-200 text-green-800 rounded-full flex items-center justify-center font-bold text-xl mx-auto mb-6 shadow-sm border-4 border-white">
                3
            </div>
            <div class="w-16 h-16 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    <!-- Using a different icon layout for borrowing -->
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <h3 class="font-bold text-xl text-gray-900 mb-3">Pinjam & Ambil</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Scan kartu anggota di loket peminjaman dan bawa pulang buku favorit. Maks 7 hari pinjam.</p>
        </div>
    </div>
</section>

<!-- BERITA & PENGUMUMAN (Static Placeholders) -->
<section class="max-w-[1200px] mx-auto px-6 py-16 mb-20">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-serif">Berita & Pengumuman</h2>
            <p class="text-gray-500">Informasi terbaru seputar kegiatan perpustakaan.</p>
        </div>
        <a href="#" class="hidden md:flex bg-green-50 text-green-700 px-5 py-2 rounded-full font-semibold hover:bg-green-100 transition-colors">
            Lainnya
        </a>
    </div>

    <!-- News Cards -->
    <div class="grid md:grid-cols-3 gap-6">
        
        <!-- News 1 -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
            <div class="h-48 bg-[#E6EBDF] relative overflow-hidden flex items-center justify-center">
                <!-- Placeholder Illustration -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-green-700/20 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="p-6">
                <span class="text-xs font-bold text-green-600 tracking-wider uppercase mb-2 block">EVENT</span>
                <h3 class="font-bold text-gray-900 text-lg mb-3 line-clamp-2 group-hover:text-green-700 transition">Bulan Literasi Nasional SMAN 4 Jember 2024</h3>
                <p class="text-gray-500 text-sm line-clamp-3">Ikuti berbagai lomba menulis dan diskusi buku bersama narasumber penulis ternama di aula sekolah selama bulan Oktober.</p>
            </div>
        </div>

        <!-- News 2 -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
            <div class="h-48 bg-[#EBEAE6] relative overflow-hidden flex items-center justify-center">
                <!-- Placeholder Illustration -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="p-6">
                <span class="text-xs font-bold text-green-600 tracking-wider uppercase mb-2 block">UPDATE</span>
                <h3 class="font-bold text-gray-900 text-lg mb-3 line-clamp-2 group-hover:text-green-700 transition">Akses E-Book Kini Lebih Mudah</h3>
                <p class="text-gray-500 text-sm line-clamp-3">Perpustakaan resmi meluncurkan portal khusus E-Book untuk siswa. Baca dan unduh karya digital bebas kuota kapan saja.</p>
            </div>
        </div>

        <!-- News 3 -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
            <div class="h-48 bg-[#DCE4E0] relative overflow-hidden flex items-center justify-center">
                <!-- Placeholder Illustration -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-green-800/20 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="p-6">
                <span class="text-xs font-bold text-green-600 tracking-wider uppercase mb-2 block">INFORMASI</span>
                <h3 class="font-bold text-gray-900 text-lg mb-3 line-clamp-2 group-hover:text-green-700 transition">Jam Operasional Khusus Selama Ujian</h3>
                <p class="text-gray-500 text-sm line-clamp-3">Selama pekan ujian tengah semester, perpustakaan buka lebih awal hingga pukul 17:30 WIB untuk mendukung siswa yang ingin belajar bersama.</p>
            </div>
        </div>

    </div>
    
    <div class="mt-8 text-center md:hidden">
        <a href="#" class="inline-flex items-center gap-2 text-green-700 font-semibold px-6 py-2 bg-green-50 rounded-full">
            Lihat Berita Lainnya
        </a>
    </div>
</section>

@endsection