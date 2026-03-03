<!DOCTYPE html>
<html>
<head>
    <title>PERPUS SMAPA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-transparent">

<nav class="bg-white shadow">
    <div class="max-w-6xl mx-auto flex justify-between items-center p-4">
        <h1 class="text-xl font-bold text-blue-900">
            PERPUS SMAPA
        </h1>

        <div class="space-x-6 text-sm">
            <a href="/" class="text-gray-600 hover:text-blue-900">Beranda</a>
            <a href="{{ route('login') }}" class="text-blue-900 font-semibold">
                Login Admin
            </a>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="bg-[#061A2F] text-gray-300 pt-16 pb-8 mt-20">

    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-10">

        <!-- KOLOM 1 -->
        <div>
            <h2 class="text-white text-lg font-bold mb-2">
                SMAN 4 JEMBER
            </h2>
            <p class="text-sm text-gray-400 mb-4">
                Est. 1988
            </p>
            <p class="text-sm leading-relaxed text-gray-400">
                Sekolah menengah atas yang berkomitmen mencetak generasi unggul,
                berkarakter, dan berprestasi di tingkat nasional maupun global.
            </p>
        </div>

        <!-- KOLOM 2 -->
        <div>
            <h3 class="text-white font-semibold mb-4 border-l-4 border-green-500 pl-3">
                Menu Utama
            </h3>
            <ul class="space-y-2 text-sm">
                <li>Beranda</li>
                <li>Prestasi</li>
                <li>Informasi</li>
                <li>Galeri Foto</li>
                <li>E-Library</li>
            </ul>
        </div>

        <!-- KOLOM 3 -->
        <div>
            <h3 class="text-white font-semibold mb-4 border-l-4 border-green-500 pl-3">
                Tautan Penting
            </h3>
            <ul class="space-y-2 text-sm">
                <li>Tentang Sekolah</li>
                <li>Visi & Misi</li>
                <li>Tenaga Pendidik</li>
                <li>Ekstrakurikuler</li>
            </ul>
        </div>

        <!-- KOLOM 4 -->
        <div>
            <h3 class="text-white font-semibold mb-4 border-l-4 border-green-500 pl-3">
                Hubungi Kami
            </h3>
            <ul class="space-y-3 text-sm">
                <li>Jl. Hayam Wuruk No.145, Jember</li>
                <li>(0331) 421819</li>
                <li>humas@sman4jember.sch.id</li>
            </ul>
        </div>

    </div>

    <!-- GARIS -->
    <div class="border-t border-gray-700 mt-12 pt-8 text-center">

        <h2 class="text-2xl md:text-3xl font-bold tracking-widest">
            <span class="text-green-400">BERKARAKTER</span>
            <span class="text-gray-400"> • </span>
            <span class="text-green-300">INOVATIF</span>
            <span class="text-gray-400"> • </span>
            <span class="text-white">BERPRESTASI</span>
        </h2>

        <p class="text-xs text-gray-500 mt-4">
            © {{ date('Y') }} SMAN 4 Jember. All rights reserved.
        </p>

    </div>

</footer>

</body>
</html>