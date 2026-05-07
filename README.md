# PHP_Laravel12_Volt

## Introduction

PHP_Laravel12_Volt is a demonstration project built with Laravel 12, Livewire, and Volt to showcase how modern reactive user interfaces can be created using Laravel without writing JavaScript frameworks.

Volt is a functional API built on top of Livewire that allows developers to create interactive components using a single Blade file containing both the component logic and the UI markup. This approach simplifies development by reducing the need for multiple files and complex boilerplate code.

In this project, a simple Counter Application is implemented using Volt. The counter value is stored in a MySQL database, allowing the application to persist state even after refreshing the page. Users can increase or decrease the counter value through interactive buttons, and the UI updates instantly without a full page reload.

This project demonstrates how Laravel developers can build reactive, database-driven interfaces using only PHP, Blade, and Livewire Volt.

---

## Project Overview

The goal of this project is to demonstrate the complete setup and usage of Volt in Laravel 12, including installation, component creation, database integration, and rendering components inside Blade views.

The project walks through the following concepts:

- Creating a Laravel 12 application

- Installing Livewire for reactive components

- Installing and configuring Volt

- Creating single-file Volt components

- Managing reactive state

- Handling user interactions using Volt actions

- Storing and retrieving data from a MySQL database

- Rendering Volt components inside Blade views

- Building a simple interactive counter application

The counter component demonstrates how Volt combines state management, backend logic, and UI rendering in a single Blade file while still leveraging Laravel's powerful features such as Eloquent models, migrations, and routing.

---

## Prerequisites

Before starting this project, ensure your system has:

- PHP 8.2 or higher

- Composer

- Node.js & NPM

- Laravel CLI (optional)

- MySQL 

Check installed versions:

```
php -v
composer -v
node -v
```

---

## Step 1: Create Laravel 12 Project

Create a new Laravel project using Composer.

```bash
composer create-project laravel/laravel PHP_Laravel12_Volt "12.*"
```
Move into the project directory:

```bash
cd PHP_Laravel12_Volt
```

---

## Step 2: Install Livewire

Volt works on top of Laravel Livewire, so we must install Livewire first.

Run the following command:

```bash
composer require livewire/livewire
```

Livewire allows developers to build dynamic interfaces using PHP instead of JavaScript frameworks.

---

## Step 3: Install Volt

Now install the Volt package.

```bash
composer require livewire/volt
```

Run the Volt installation command:

```bash
php artisan volt:install
```
This command registers Volt in the application and prepares the folders where Volt components will be stored.

---

## Step 4: Setup Database

Update .env:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_volt
DB_USERNAME=root
DB_PASSWORD=
```
Then Run Migration Command:

```bash
php artisan migrate
```
---

## Step 5: Create Counter Model and Migration

Now create a model and migration for storing the counter value.

Run the command:

```bash
php artisan make:model Counter -m
```

This will generate:

```
app/Models/Counter.php
database/migrations/create_counters_table.php
```

### Migration Table

Open the migration file:

```
database/migrations/xxxx_create_counters_table.php
```
Update it like this:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
```
Then Run Migration Command:

```bash
php artisan migrate
```

### Model

Open:

```
app/Models/Counter.php
```
Update the model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = ['count'];
}
```

---

## Step 6: Volt Component Directory

Volt components are stored inside:

```bash
resources/views/livewire
```

Volt components are Blade files containing both PHP logic and UI markup.

Example structure after Volt installation:

```
resources
└── views
    ├── livewire
```

---

## Step 7: Create a Volt Component

Create a new Volt component named counter.

```bash
php artisan make:volt counter
```

This command generates:

```
resources/views/livewire/counter.blade.php
```

---

## Step 8: Volt Counter Component Code

Open the file:

```
resources/views/livewire/counter.blade.php
```

Add the following code:

```blade
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
```

---

## Step 9: Display Volt Component in View

Create a new file:

```
resources/views/volt-demo.blade.php
```

Add the following code:

```blade
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
```

---

## Step 10:  Routes

Open:

```
routes/web.php
```

Add a new route.

```php
use Illuminate\Support\Facades\Route;

Route::get('/volt-demo', function () {
    return view('volt-demo');
});
```

---

## Step 11:  Run the Application

Start the Laravel server:

```bash
php artisan serve
```

Visit:

```bash
http://127.0.0.1:8000/volt-demo
```

You will see the Volt Counter Application.

Clicking buttons updates the counter without reloading the page.

---

## Output

<img width="1919" height="1027" alt="Screenshot 2026-03-12 175017" src="https://github.com/user-attachments/assets/2590e90d-1615-4a1d-a3eb-8c2281d93939" />

---

## Project Structure

```
PHP_Laravel12_Volt
│
├── app
│   └── Models
│       └── Counter.php
│
├── bootstrap
│
├── config
│
├── database
│   └── migrations
│       └── xxxx_xx_xx_create_counters_table.php
│
├── public
│
├── resources
│   └── views
│       ├── livewire
│       │   └── counter.blade.php
│       │
│       └── volt-demo.blade.php
│
├── routes
│   └── web.php
│
├── storage
│
├── tests
│
├── .env
│
└── vendor
```

---

Your PHP_Laravel12_Volt Project is now ready!

<<<<<<< HEAD


=======
>>>>>>> development
