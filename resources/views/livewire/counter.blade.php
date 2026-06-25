<?php

use function Livewire\Volt\{state, mount};

use App\Models\Counter;

state([
    'counters' => [],
    'newCounterName' => '',
    'step' => 1,
    'confirmingReset' => false,
    'resetTargetId' => null,
    'history' => [],
]);

mount(function () {
    $records = Counter::all();

    if ($records->isEmpty()) {
        $records = collect([
            Counter::create(['name' => 'Main Counter', 'count' => 0, 'target' => 100])
        ]);
    }

    $mapped = [];

    foreach ($records as $record) {
        $percent = 0;

        if ($record->target > 0) {
            $percent = min(100, round(($record->count / $record->target) * 100));
        }

        $mapped[] = [
            'id' => $record->id,
            'name' => $record->name,
            'count' => $record->count,
            'target' => $record->target,
            'message' => '',
            'percent' => $percent,
        ];
    }

    $this->counters = $mapped;
});

$addCounter = function () {
    $name = trim($this->newCounterName);

    if ($name === '') {
        return;
    }

    $record = Counter::create([
        'name' => $name,
        'count' => 0,
        'target' => 100,
    ]);

    $this->counters[] = [
        'id' => $record->id,
        'name' => $record->name,
        'count' => $record->count,
        'target' => $record->target,
        'message' => '',
        'percent' => 0,
    ];

    $this->newCounterName = '';
};

$removeCounter = function ($id) {
    $newList = [];

    foreach ($this->counters as $row) {
        if ($row['id'] != $id) {
            $newList[] = $row;
        }
    }

    $this->counters = $newList;

    $record = Counter::find($id);

    if ($record) {
        $record->delete();
    }
};

$increment = function ($id) {
    $list = $this->counters;

    foreach ($list as $i => $row) {
        if ($row['id'] == $id) {

            if ($row['count'] >= $row['target']) {
                $list[$i]['message'] = 'Maximum limit reached';
                $this->counters = $list;
                return;
            }

            $oldValue = $row['count'];
            $newValue = $row['count'] + $this->step;

            if ($newValue > $row['target']) {
                $newValue = $row['target'];
            }

            $percent = 0;

            if ($row['target'] > 0) {
                $percent = min(100, round(($newValue / $row['target']) * 100));
            }

            $list[$i]['count'] = $newValue;
            $list[$i]['percent'] = $percent;
            $list[$i]['message'] = 'Counter Increased';

            $this->counters = $list;

            $record = Counter::find($id);
            $record->update(['count' => $newValue]);

            $logEntry = [
                'counter' => $row['name'],
                'action' => '+' . $this->step,
                'old' => $oldValue,
                'new' => $newValue,
                'time' => now()->format('h:i:s A'),
            ];

            $newHistory = $this->history;
            array_unshift($newHistory, $logEntry);
            $this->history = array_slice($newHistory, 0, 8);

            if ($newValue >= $row['target']) {
                $this->dispatch('milestone-reached');
            }

            $this->dispatch('play-click-sound');

            return;
        }
    }
};

$decrement = function ($id) {
    $list = $this->counters;

    foreach ($list as $i => $row) {
        if ($row['id'] == $id) {

            if ($row['count'] <= 0) {
                $list[$i]['message'] = 'Counter cannot go below 0';
                $this->counters = $list;
                return;
            }

            $oldValue = $row['count'];
            $newValue = $row['count'] - $this->step;

            if ($newValue < 0) {
                $newValue = 0;
            }

            $percent = 0;

            if ($row['target'] > 0) {
                $percent = min(100, round(($newValue / $row['target']) * 100));
            }

            $list[$i]['count'] = $newValue;
            $list[$i]['percent'] = $percent;
            $list[$i]['message'] = 'Counter Decreased';

            $this->counters = $list;

            $record = Counter::find($id);
            $record->update(['count' => $newValue]);

            $logEntry = [
                'counter' => $row['name'],
                'action' => '-' . $this->step,
                'old' => $oldValue,
                'new' => $newValue,
                'time' => now()->format('h:i:s A'),
            ];

            $newHistory = $this->history;
            array_unshift($newHistory, $logEntry);
            $this->history = array_slice($newHistory, 0, 8);

            $this->dispatch('play-click-sound');

            return;
        }
    }
};

$undoLast = function () {
    $history = $this->history;

    if (count($history) === 0) {
        return;
    }

    $last = $history[0];

    $list = $this->counters;

    foreach ($list as $i => $row) {
        if ($row['name'] === $last['counter']) {

            $percent = 0;

            if ($row['target'] > 0) {
                $percent = min(100, round(($last['old'] / $row['target']) * 100));
            }

            $list[$i]['count'] = $last['old'];
            $list[$i]['percent'] = $percent;
            $list[$i]['message'] = 'Undo Successful';

            $record = Counter::find($row['id']);
            $record->update(['count' => $last['old']]);

            break;
        }
    }

    $this->counters = $list;

    array_shift($history);
    $this->history = $history;
};

$confirmReset = function ($id) {
    $this->confirmingReset = true;
    $this->resetTargetId = $id;
};

$cancelReset = function () {
    $this->confirmingReset = false;
    $this->resetTargetId = null;
};

$resetCounter = function () {
    $targetId = $this->resetTargetId;
    $list = $this->counters;

    foreach ($list as $i => $row) {
        if ($row['id'] == $targetId) {

            $oldValue = $row['count'];

            $list[$i]['count'] = 0;
            $list[$i]['percent'] = 0;
            $list[$i]['message'] = 'Counter Reset Successful';

            $record = Counter::find($targetId);
            $record->update(['count' => 0]);

            $logEntry = [
                'counter' => $row['name'],
                'action' => 'reset',
                'old' => $oldValue,
                'new' => 0,
                'time' => now()->format('h:i:s A'),
            ];

            $newHistory = $this->history;
            array_unshift($newHistory, $logEntry);
            $this->history = array_slice($newHistory, 0, 8);

            break;
        }
    }

    $this->counters = $list;
    $this->confirmingReset = false;
    $this->resetTargetId = null;
};

?>

<div class="flex flex-col items-center space-y-8" x-data="{}" x-on:milestone-reached.window="window.__showMilestone()" x-on:play-click-sound.window="window.__playClickSound()">

    <h2 class="text-3xl font-extrabold text-indigo-400 tracking-wide">
        Laravel Volt Counter
    </h2>

    <div class="flex gap-3 w-full max-w-md">
        <input type="text" wire:model="newCounterName" placeholder="New counter name..."
            class="flex-1 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500">

        <button wire:click="addCounter"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-xl">
            Add
        </button>
    </div>

    <div class="flex items-center gap-3">
        <span class="text-gray-400 text-sm">Step:</span>

        <select wire:model="step" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
            <option value="1">+1 / -1</option>
            <option value="5">+5 / -5</option>
            <option value="10">+10 / -10</option>
            <option value="50">+50 / -50</option>
        </select>
    </div>

    <div class="w-full max-w-2xl space-y-6">
        @foreach($counters as $counter)
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-inner">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">{{ $counter['name'] }}</h3>

                <button wire:click="removeCounter({{ $counter['id'] }})"
                    class="text-red-400 hover:text-red-500 text-sm font-semibold">
                    Remove
                </button>
            </div>

            @if($counter['message'])
            <div class="px-4 py-2 mb-4 rounded-xl bg-indigo-500/20 border border-indigo-500 text-indigo-300 text-sm font-semibold">
                {{ $counter['message'] }}
            </div>
            @endif

            <div class="flex justify-center mb-4">
                <div class="w-40 h-40 rounded-full bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center shadow-[0_0_40px_rgba(99,102,241,0.6)] border-4 border-white/20">
                    <span class="text-5xl font-black text-white">{{ $counter['count'] }}</span>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-400 mb-2">
                    <span>Progress</span>
                    <span>{{ $counter['percent'] }}% / Target: {{ $counter['target'] }}</span>
                </div>

                <div class="w-full bg-gray-700 rounded-full h-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-4 transition-all duration-500" style="width: {{ $counter['percent'] }}%"></div>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                <button wire:click="decrement({{ $counter['id'] }})"
                    class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 transition font-bold text-white">
                    − Decrease
                </button>

                <button wire:click="increment({{ $counter['id'] }})"
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition font-bold text-white">
                    + Increase
                </button>

                <button wire:click="confirmReset({{ $counter['id'] }})"
                    class="px-5 py-2 rounded-xl bg-yellow-400 hover:bg-yellow-500 transition font-bold text-black">
                    Reset
                </button>
            </div>

        </div>
        @endforeach
    </div>

    @if(count($history) > 0)
    <button wire:click="undoLast"
        class="px-6 py-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-white font-bold shadow-xl">
        ↩ Undo Last Action
    </button>
    @endif

    @if(count($history) > 0)
    <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-5">
        <h3 class="text-gray-400 text-sm mb-3 font-semibold">History Log</h3>

        <div class="space-y-2 max-h-40 overflow-y-auto">
            @foreach($history as $entry)
            <div class="text-xs text-gray-300 flex justify-between border-b border-white/5 pb-1">
                <span>{{ $entry['counter'] }}: {{ $entry['action'] }} ({{ $entry['old'] }} -> {{ $entry['new'] }})</span>
                <span class="text-gray-500">{{ $entry['time'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($confirmingReset)
    <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div class="bg-gray-900 border border-white/10 rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-3">Are you sure?</h3>
            <p class="text-gray-400 mb-6">This will reset the counter value to 0.</p>

            <div class="flex justify-center gap-4">
                <button wire:click="cancelReset"
                    class="px-5 py-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-white font-bold">
                    Cancel
                </button>

                <button wire:click="resetCounter"
                    class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold">
                    Yes, Reset
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    if (typeof window.__playClickSound !== 'function') {
        window.__playClickSound = function () {
            try {
                var audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQoAAAAA');
                audio.volume = 0.3;
                audio.play().catch(function () {});
            } catch (e) {}

            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        };
    }

    if (typeof window.__showMilestone !== 'function') {
        window.__showMilestone = function () {
            var el = document.createElement('div');
            el.innerHTML = '🎉';
            el.style.position = 'fixed';
            el.style.top = '40%';
            el.style.left = '50%';
            el.style.fontSize = '80px';
            el.style.zIndex = '9999';
            el.style.transform = 'translate(-50%, -50%)';
            document.body.appendChild(el);

            setTimeout(function () {
                el.remove();
            }, 1500);
        };
    }
</script>