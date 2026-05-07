<?php

use function Livewire\Volt\{state, mount};

use App\Models\Counter;

state([
    'count' => 0,
    'message' => '',
    'totalClicks' => 0,
]);

mount(function () {

    $counter = Counter::first();

    if (!$counter) {

        $counter = Counter::create([
            'count' => 0
        ]);

    }

    $this->count = $counter->count;

});

$saveCounter = function () {

    Counter::first()->update([
        'count' => $this->count
    ]);

};

$increment = function () {

    if ($this->count >= 100) {

        $this->message = 'Maximum limit reached';

        return;

    }

    $this->count++;

    $this->totalClicks++;

    $this->saveCounter();

    $this->message = 'Counter Increased';

};

$incrementFive = function () {

    if ($this->count >= 95) {

        $this->message = 'Maximum limit reached';

        return;

    }

    $this->count += 5;

    $this->totalClicks++;

    $this->saveCounter();

    $this->message = 'Counter Increased by 5';

};

$decrement = function () {

    if ($this->count <= 0) {

        $this->message = 'Counter cannot go below 0';

        return;

    }

    $this->count--;

    $this->totalClicks++;

    $this->saveCounter();

    $this->message = 'Counter Decreased';

};

$resetCounter = function () {

    $this->count = 0;

    $this->saveCounter();

    $this->message = 'Counter Reset Successful';

};

?>

<div class="flex flex-col items-center space-y-8">

    <!-- Alert -->
    @if($message)

        <div class="px-5 py-3 rounded-xl bg-indigo-500/20 border border-indigo-500 text-indigo-300 shadow-lg font-semibold">

            {{ $message }}

        </div>

    @endif

    <!-- Title -->
    <h2 class="text-3xl font-extrabold text-indigo-400 tracking-wide">
        Laravel Volt Counter
    </h2>

    <!-- Counter Circle -->
    <div class="relative">

        <div
            class="w-56 h-56 rounded-full bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center shadow-[0_0_60px_rgba(99,102,241,0.7)] border-4 border-white/20">

            <span class="text-8xl font-black text-white">
                {{ $count }}
            </span>

        </div>

    </div>

    <!-- Progress Bar -->
    <div class="w-full max-w-md">

        <div class="flex justify-between text-sm text-gray-400 mb-2">
            <span>Progress</span>
            <span>{{ $count }}%</span>
        </div>

        <div class="w-full bg-gray-700 rounded-full h-4 overflow-hidden">

            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-4 transition-all duration-500"
                style="width: {{ $count }}%">

            </div>

        </div>

    </div>

    <!-- Buttons -->
    <div class="flex flex-wrap justify-center gap-4">

        <!-- Decrease -->
        <button wire:click="decrement"
            class="px-6 py-3 rounded-xl bg-red-500 hover:bg-red-600 transition duration-300 font-bold shadow-xl text-white">

            − Decrease

        </button>

        <!-- Increase -->
        <button wire:click="increment"
            class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 font-bold shadow-xl text-white">

            + Increase

        </button>

        <!-- Increase 5 -->
        <button wire:click="incrementFive"
            class="px-6 py-3 rounded-xl bg-green-500 hover:bg-green-600 transition duration-300 font-bold shadow-xl text-white">

            +5 Increase

        </button>

        <!-- Reset -->
        <button wire:click="resetCounter"
            class="px-6 py-3 rounded-xl bg-yellow-400 hover:bg-yellow-500 transition duration-300 font-bold shadow-xl text-black">

            Reset

        </button>

    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 gap-6 w-full max-w-md">

        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">

            <h3 class="text-gray-400 text-sm mb-2">
                Total Clicks
            </h3>

            <p class="text-3xl font-bold text-indigo-400">
                {{ $totalClicks }}
            </p>

        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">

            <h3 class="text-gray-400 text-sm mb-2">
                Last Updated
            </h3>

            <p class="text-sm text-white">
                {{ now()->format('d M Y h:i:s A') }}
            </p>

        </div>

    </div>

</div>