@extends('layouts.public')

@section('content')

<section class="min-h-screen flex items-center justify-center 
bg-gradient-to-b from-green-100 to-green-50">

    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-md">

        <!-- TITLE -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">
                <span class="text-gray-800">Buat</span>
                <span class="text-green-600"> Akun</span>
            </h2>
            <p class="text-gray-500 text-sm mt-2">
                Admin E-Library SMAN 4 JEMBER
            </p>
        </div>

        <!-- ERROR MESSAGE -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nama -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border rounded-lg 
                    focus:ring-2 focus:ring-green-400 focus:outline-none">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
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

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
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
                Daftar
            </button>

        </form>

        <!-- Link Login -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" 
                   class="text-green-600 hover:underline font-medium">
                    Login
                </a>
            </p>
        </div>

    </div>

    <!-- SCRIPT -->
    <script>
        document.getElementById("showPassword").addEventListener("change", function () {
            const password = document.getElementById("password");
            const confirm = document.getElementById("password_confirmation");

            if (this.checked) {
                password.type = "text";
                confirm.type = "text";
            } else {
                password.type = "password";
                confirm.type = "password";
            }
        });
    </script>

</section>

@endsection