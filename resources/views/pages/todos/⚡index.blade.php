<?php

use App\Interfaces\TodoInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.todoapp')] class extends Component
{
    public string $filter = 'all'; // all | done | pending

    public function toggle($id, TodoInterface $todos)
    {
        $todos->toggleDone($id);
    }

    public function delete($id, TodoInterface $todos)
    {
        $todos->delete($id);
        session()->flash('message', 'Todo deleted!');
    }

    public function render()
    {
        $todos = app(TodoInterface::class)->all();

        if ($this->filter === 'done') {
            $todos = $todos->where('is_done', true);
        } elseif ($this->filter === 'pending') {
            $todos = $todos->where('is_done', false);
        }

        return $this->view()->with('todos', $todos);
    }
};
?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Todos</h1>
        <a href="{{ route('todos.create') }}" class="btn btn-primary">+ New Todo</a>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="btn-group mb-3">
        <button wire:click="$set('filter', 'all')" class="btn btn-outline-secondary {{ $filter === 'all' ? 'active' : '' }}">All</button>
        <button wire:click="$set('filter', 'pending')" class="btn btn-outline-secondary {{ $filter === 'pending' ? 'active' : '' }}">Pending</button>
        <button wire:click="$set('filter', 'done')" class="btn btn-outline-secondary {{ $filter === 'done' ? 'active' : '' }}">Done</button>
    </div>

    <div class="list-group">
        @forelse ($todos as $todo)
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div class="form-check me-3 mt-1">
                    <input class="form-check-input" type="checkbox"
                        wire:click="toggle({{ $todo->id }})"
                        @checked($todo->is_done)>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 {{ $todo->is_done ? 'text-decoration-line-through text-muted' : '' }}">{{ $todo->title }}</h5>
                    <p class="mb-1 text-muted small">{{ $todo->description }}</p>
                    @if ($todo->due_at)
                        <span class="badge bg-info text-dark">Due {{ $todo->due_at->format('D, d M Y H:i') }}</span>
                    @endif
                </div>
                <div class="d-flex gap-2 shrink-0">
                    <a href="{{ route('todos.edit', $todo->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <button wire:click="delete({{ $todo->id }})" wire:confirm="Delete this todo?" class="btn btn-sm btn-outline-danger">Delete</button>
                </div>
            </div>
        @empty
            <p class="text-muted">No todos yet — create your first one above.</p>
        @endforelse
    </div>
</div>
