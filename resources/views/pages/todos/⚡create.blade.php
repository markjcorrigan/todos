<?php

use App\Interfaces\TodoInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.todoapp')] class extends Component
{
    public string $title = '';
    public string $description = '';
    public string $due_at = '';

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'due_at'      => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:' . now()->subMinute()->format('Y-m-d\TH:i'),
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'        => 'Please enter a title.',
            'title.max'             => 'Title cannot be longer than 255 characters.',
            'description.max'       => 'Description cannot be longer than 5000 characters.',
            'due_at.date_format'    => 'Please pick a valid date and time.',
            'due_at.after_or_equal' => 'Due date/time cannot be in the past.',
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function save(TodoInterface $todos)
    {
        $validated = $this->validate();

        $todos->create($validated);

        session()->flash('message', 'Todo saved!');
        $this->redirect(route('todos.index'));
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div>
    <h1 class="h3 mb-4">New Todo</h1>

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
        <button wire:click="save" class="btn btn-primary">Save</button>
        <a href="{{ route('todos.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
