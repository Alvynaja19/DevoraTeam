@extends('layouts.public')

@section('content')

<section class="min-h-screen flex items-center justify-center 
bg-gradient-to-b from-green-100 to-green-50">

    <div class="text-center max-w-5xl px-6">

        <!-- ICON -->
        <div class="mx-auto w-24 h-24 bg-green-600 
        rounded-2xl flex items-center justify-center shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="h-12 w-12 text-white" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 6v6l4 2" />
            </svg>
        </div>

        <!-- TITLE -->
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            <span class="text-gray-800">E-Library</span>
            <span class="text-green-600"> SMAN 4 JEMBER</span>
        </h1>

        <p class="text-gray-600 max-w-2xl mx-auto mb-10">
            Sistem perpustakaan digital untuk mendukung literasi dan
            akses pembelajaran siswa secara modern dan efisien.
        </p>

        <!-- FEATURES -->
        <div class="grid md:grid-cols-3 gap-6 mb-10">

            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                <h3 class="font-semibold text-lg mb-2 text-gray-800">
                    Koleksi Digital
                </h3>
                <p class="text-sm text-gray-500">
                    Ribuan buku dan referensi tersedia dalam format digital.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                <h3 class="font-semibold text-lg mb-2 text-gray-800">
                    Pencarian Mudah
                </h3>
                <p class="text-sm text-gray-500">
                    Sistem pencarian cepat dengan filter kategori buku.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                <h3 class="font-semibold text-lg mb-2 text-gray-800">
                    Akses 24/7
                </h3>
                <p class="text-sm text-gray-500">
                    Bisa diakses kapan saja dan di mana saja.
                </p>
            </div>

        </div>

        <!-- BUTTON -->
        <a href="{{ route('login') }}" 
           class="inline-block px-8 py-3 bg-green-600 
           text-white rounded-full shadow-md 
           hover:bg-green-700 transition">
            Login Admin
        </a>

    </div>

</section>

@endsection