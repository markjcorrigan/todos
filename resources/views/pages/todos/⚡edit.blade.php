<?php

use App\Interfaces\TodoInterface;
use App\Models\Todo;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.todoapp')] class extends Component
{
    public string $title = '';
    public string $description = '';
    public string $due_at = '';
    public bool $is_done = false;
    public int $todoId;

    public function mount(Todo $todo)
    {
        $this->todoId      = $todo->id;
        $this->title       = $todo->title;
        $this->description = $todo->description ?? '';
        $this->is_done      = $todo->is_done;
        $this->due_at       = optional($todo->due_at)->format('Y-m-d\TH:i') ?? '';
    }

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'due_at'      => 'nullable|date_format:Y-m-d\TH:i',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'     => 'Please enter a title.',
            'title.max'          => 'Title cannot be longer than 255 characters.',
            'description.max'    => 'Description cannot be longer than 5000 characters.',
            'due_at.date_format' => 'Please pick a valid date and time.',
        ];
    }

    public function updated($property)
    {
        if (in_array($property, ['title', 'description', 'due_at'])) {
            $this->validateOnly($property);
        }
    }

    public function update(TodoInterface $todos)
    {
        $validated = $this->validate();

        $validated['is_done'] = $this->is_done;

        $todos->update($this->todoId, $validated);

        session()->flash('message', 'Todo updated!');
        $this->redirect(route('todos.index'));
    }

    public function delete(TodoInterface $todos)
    {
        $todos->delete($this->todoId);
        session()->flash('message', 'Todo deleted!');
        $this->redirect(route('todos.index'));
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    <h1 class="h3 mb-4">Edit Todo</h1>

    <div class="form-check mb-3">
        <input type="checkbox" wire:model="is_done" class="form-check-input" id="is_done">
        <label for="is_done" class="form-check-label">Done</label>
    </div>

    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" wire:model.blur="title" class="form-control @error('title') is-invalid @enderror">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" wire:model.blur="description" rows="3" maxlength="5000" class="form-control @error('description') is-invalid @enderror"></textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">{{ strlen($description) }}/5000 characters</div>
    </div>

    <div class="mb-4">
        <label for="due_at" class="form-label">Due date/time</label>
        <input type="datetime-local" id="due_at" wire:model.blur="due_at" class="form-control @error('due_at') is-invalid @enderror">
        @error('due_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
        <button wire:click="update" class="btn btn-primary">Update</button>
        <button wire:click="delete" wire:confirm="Delete this todo?" class="btn btn-outline-danger">Delete</button>
        <a href="{{ route('todos.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
