<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel 12 Volt Advanced Counter</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles

</head>

<body class="bg-black min-h-screen flex items-center justify-center overflow-hidden">

    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-black to-purple-950 opacity-95"></div>

    <!-- Glow -->
    <div class="absolute w-96 h-96 bg-indigo-600 rounded-full blur-3xl opacity-20 top-10 left-10"></div>

    <div class="absolute w-96 h-96 bg-purple-600 rounded-full blur-3xl opacity-20 bottom-10 right-10"></div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-2xl px-6">

        <div class="backdrop-blur-2xl bg-white/5 border border-white/10 rounded-3xl shadow-2xl p-10">

            <!-- Header -->
            <div class="text-center mb-10">

                <h1 class="text-5xl font-extrabold text-white mb-3">
                    Laravel 12 Volt
                </h1>

                <p class="text-gray-400 text-lg">
                    Advanced Livewire Volt Counter Application
                </p>

            </div>

            <!-- Counter Component -->
            <div class="bg-black/40 border border-gray-700 rounded-2xl p-10 shadow-inner">

                <livewire:counter />

            </div>

        </div>

        <!-- Footer -->
        <div class="text-center text-gray-500 mt-6 text-sm">

            Built with Laravel 12 • Livewire • Volt • TailwindCSS

        </div>

    </div>

    @livewireScripts

</body>

</html>