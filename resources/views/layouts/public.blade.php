<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Library | SMAN 4 JEMBER</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f0ece1] text-gray-800 antialiased font-sans">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex justify-between items-center bg-white/50 backdrop-blur-lg">
            
            <!-- Logo Section -->
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-700 text-white rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-green-800 leading-tight text-lg tracking-wide">SMAN 4 JEMBER</h1>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-medium">Library Center</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="/" class="text-green-700 font-semibold hover:text-green-800 transition">Beranda</a>
                <a href="#" class="hover:text-green-700 transition">Koleksi Buku</a>
                <a href="#" class="hover:text-green-700 transition">Katalog</a>
                <a href="#" class="hover:text-green-700 transition">Tentang Kami</a>
            </div>

            <!-- Login Button -->
            <div class="hidden md:block">
                <a href="{{ route('login') }}" class="px-6 py-2.5 bg-green-200 text-green-800 font-semibold rounded-full hover:bg-green-300 transition-colors">
                    Login
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-gray-600 hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-green-800 text-white pt-20 pb-8 mt-24">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <!-- Branding -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-white text-green-800 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="font-bold text-xl tracking-wide">SMAN 4 JEMBER</h2>
                    </div>
                    <p class="text-green-100/80 text-sm leading-relaxed mb-6">
                        Menjadi pusat sumber belajar yang inovatif dan inspiratif untuk mewujudkan generasi literat yang unggul dan kompetitif.
                    </p>
                </div>

                <!-- Tautan Cepat -->
                <div>
                    <h3 class="font-semibold text-lg mb-6 tracking-wide">Tautan Cepat</h3>
                    <ul class="space-y-4 text-sm text-green-100/80">
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-green-400 rounded-full"></span> Katalog Online (OPAC)</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-green-400 rounded-full"></span> Koleksi Digital</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-green-400 rounded-full"></span> Keanggotaan</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><span class="w-1 h-1 bg-green-400 rounded-full"></span> Donasi Buku</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div>
                    <h3 class="font-semibold text-lg mb-6 tracking-wide">Kontak Kami</h3>
                    <ul class="space-y-4 text-sm text-green-100/80">
                        <li class="flex items-start gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. Hayam Wuruk No.145, Sempusari, Kec. Kaliwates, Kabupaten Jember, Jawa Timur 68131</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>perpus@sman4jember.sch.id</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>(0331) 421819</span>
                        </li>
                    </ul>
                </div>

                <!-- Jadwal -->
                <div>
                    <h3 class="font-semibold text-lg mb-6 tracking-wide">Jam Layanan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center text-green-100/80 border-b border-green-700/50 pb-2">
                            <span>Senin - Kamis</span>
                            <span class="font-medium">07.00 - 15.30</span>
                        </div>
                        <div class="flex justify-between items-center text-green-100/80 border-b border-green-700/50 pb-2">
                            <span>Jumat</span>
                            <span class="font-medium">07.00 - 11.30</span>
                        </div>
                        <div class="flex justify-between items-center text-red-300 pt-1">
                            <span>Sabtu - Minggu</span>
                            <span class="font-medium">Tutup</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-green-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-green-100/60 font-medium tracking-wide">
                <p>&copy; {{ date('Y') }} Perpustakaan SMAN 4 Jember. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>