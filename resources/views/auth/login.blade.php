@extends('layouts.public')

@section('content')

<section class="min-h-screen flex items-center justify-center 
bg-gradient-to-b from-green-100 to-green-50">

    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-md">

        <!-- TITLE -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">
                <span class="text-gray-800">Login</span>
                <span class="text-green-600"> Admin</span>
            </h2>
            <p class="text-gray-500 text-sm mt-2">
                E-Library SMAN 4 JEMBER
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg 
                    focus:ring-2 focus:ring-green-400 focus:outline-none">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border rounded-lg
                    focus:ring-2 focus:ring-green-400 focus:outline-none">
            </div>

            <!-- Checkbox + Lupa Password -->
            <div class="flex items-center justify-between mb-6">
                
                <div class="flex items-center">
                    <input type="checkbox" id="showPassword"
                        class="w-4 h-4 text-green-600 border-gray-300 rounded">
                    <label for="showPassword" class="ml-2 text-sm text-gray-600">
                        Tampilkan Password
                    </label>
                </div>

                <a href="{{ route('password.request') }}" 
                   class="text-sm text-gray-600 hover:text-green-600">
                    Lupa Password?
                </a>

            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full py-3 bg-green-600 text-white 
                rounded-full shadow-md hover:bg-green-700 transition">
                Login
            </button>

        </form>

        <!-- Buat Akun -->
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" 
                   class="text-green-600 hover:underline font-medium">
                    Buat Akun
                </a>
            </p>
        </div>

        <!-- Back -->
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-green-600 hover:underline">
                ← Kembali ke Beranda
            </a>
        </div>

    </div>

    <!-- SCRIPT -->
    <script>
        document.getElementById("showPassword").addEventListener("change", function () {
            const password = document.getElementById("password");

            if (this.checked) {
                password.type = "text";
            } else {
                password.type = "password";
            }
        });
    </script>

</section>

@endsection