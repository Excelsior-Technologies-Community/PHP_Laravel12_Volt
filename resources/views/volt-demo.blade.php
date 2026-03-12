<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel 12 Volt Demo</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
</head>

<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">

    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-black opacity-80"></div>

    <!-- Main Card -->
    <div class="relative z-10 w-full max-w-xl">

        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl shadow-2xl p-10 text-center">

            <!-- Title -->
            <h1 class="text-4xl font-bold mb-2">
                Laravel 12 Volt Demo
            </h1>

            <p class="text-gray-400 mb-8">
                Reactive UI with Livewire Volt
            </p>

            <!-- Counter Component -->
            <div class="bg-black/40 rounded-xl p-8 border border-gray-700 shadow-inner">
                <livewire:counter />
            </div>

        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            Built with Laravel 12 • Livewire • Volt
        </p>

    </div>

    @livewireScripts

</body>

</html>