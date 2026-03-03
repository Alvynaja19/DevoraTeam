@extends('layouts.public')

@section('content')

<section class="min-h-screen bg-gradient-to-b from-green-100 to-green-50 py-12">

    <div class="max-w-6xl mx-auto px-6">

        <!-- TITLE -->
        <div class="mb-10">
            <h2 class="text-3xl font-bold">
                <span class="text-gray-800">Dashboard</span>
                <span class="text-green-600"> Admin</span>
            </h2>
            <p class="text-gray-600 mt-2">
                Selamat datang di Sistem E-Library SMAN 4 Jember
            </p>
        </div>

        <!-- MENU VERTICAL -->
        <div class="space-y-5 max-w-md">

            <!-- Manajemen Buku -->
            <a href="{{ route('books.index') }}"
               class="block bg-white hover:bg-green-50 transition 
               p-6 rounded-2xl shadow-md border border-green-100">
                <h3 class="text-lg font-semibold text-green-700">
                    📚 Manajemen Buku
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Tambah, edit, dan kelola seluruh data buku.
                </p>
            </a>

            <!-- Data Member -->
            <a href="{{ route('members.index') }}"
               class="block bg-white hover:bg-green-50 transition 
               p-6 rounded-2xl shadow-md border border-green-100">
                <h3 class="text-lg font-semibold text-green-700">
                    👥 Data Member
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola data anggota perpustakaan.
                </p>
            </a>

            <!-- Peminjaman -->
            <a href="{{ route('borrowings.index') }}"
               class="block bg-white hover:bg-green-50 transition 
               p-6 rounded-2xl shadow-md border border-green-100">
                <h3 class="text-lg font-semibold text-green-700">
                    📖 Peminjaman
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Atur peminjaman dan pengembalian buku.
                </p>
            </a>

        </div>

        <!-- Logout -->
        <div class="mt-12">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-6 py-2 bg-red-500 text-white rounded-full 
                    hover:bg-red-600 transition shadow">
                    Logout
                </button>
            </form>
        </div>

    </div>

</section>

@endsection