<?php

use function Livewire\Volt\{state, mount};
use App\Models\Counter;

state([
    'count' => 0
]);

mount(function () {

    $counter = Counter::first();

    if (!$counter) {
        $counter = Counter::create(['count' => 0]);
    }

    $this->count = $counter->count;

});

$increment = function () {

    $this->count++;

    Counter::first()->update([
        'count' => $this->count
    ]);

};

$decrement = function () {

    $this->count--;

    Counter::first()->update([
        'count' => $this->count
    ]);

};

?>

<div class="flex flex-col items-center space-y-8">

    <!-- Title -->
    <h2 class="text-xl font-semibold text-gray-300">
        Laravel Volt Counter
    </h2>

    <!-- Counter Number -->
    <div class="text-7xl font-bold text-indigo-400 drop-shadow-lg">
        {{ $count }}
    </div>

    <!-- Buttons -->
    <div class="flex gap-6">

        <button 
            wire:click="decrement"
            class="px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition duration-200 font-semibold shadow-lg text-white">
            −
        </button>

        <button 
            wire:click="increment"
            class="px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition duration-200 font-semibold shadow-lg text-white">
            +
        </button>

    </div>

</div>