<?php

namespace App\Repositories;

use App\Interfaces\TodoInterface;
use App\Models\Todo;
use Illuminate\Support\Collection;

class TodoRepository implements TodoInterface
{
    public function all(): Collection
    {
        return Todo::latest()->get();
    }

    public function forDateRange(\Carbon\Carbon $start, \Carbon\Carbon $end): Collection
    {
        return Todo::whereBetween('due_at', [$start, $end])->get();
    }

    public function create(array $data): Todo
    {
        return Todo::create($data);
    }

    public function update(int $id, array $data): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->update($data);

        return $todo;
    }

    public function delete(int $id): void
    {
        Todo::findOrFail($id)->delete();
    }

    public function toggleDone(int $id): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->update(['is_done' => ! $todo->is_done]);

        return $todo;
    }
}
