<?php

use App\Livewire\Todos\TodoBackupRestore;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::livewire('/todos', 'pages::todos.index')->name('todos.index');
Route::livewire('/todos/create', 'pages::todos.create')->name('todos.create');
Route::livewire('/todos/{todo}/edit', 'pages::todos.edit')->name('todos.edit');
Route::livewire('/todos-calendar', 'pages::todos.calendar')->name('todos.calendar');
Route::get('/todos-backup', TodoBackupRestore::class)->name('todos.backup');

require __DIR__.'/settings.php';
