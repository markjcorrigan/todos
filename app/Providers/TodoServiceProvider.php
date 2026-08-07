<?php

namespace App\Providers;

use App\Interfaces\TodoInterface;
use App\Repositories\TodoRepository;
use Illuminate\Support\ServiceProvider;

class TodoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TodoInterface::class, TodoRepository::class);
    }
}
