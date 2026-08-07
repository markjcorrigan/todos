<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Todo Backups</h1>
        <button wire:click="createBackup" class="btn btn-primary">Create Backup Now</button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="list-group">
        @forelse ($backups as $backup)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $backup['name'] }}</div>
                    <div class="small text-muted">{{ $backup['date'] }} &middot; {{ number_format($backup['size'] / 1024, 1) }} KB</div>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="restoreFromFile('{{ $backup['path'] }}')"
                        wire:confirm="This will REPLACE all current todos with the contents of this backup. Continue?"
                        class="btn btn-sm btn-outline-primary">Restore</button>
                    <button wire:click="deleteBackup('{{ $backup['path'] }}')"
                        wire:confirm="Delete this backup file permanently?"
                        class="btn btn-sm btn-outline-danger">Delete</button>
                </div>
            </div>
        @empty
            <p class="text-muted">No backups yet — click "Create Backup Now" above.</p>
        @endforelse
    </div>
</div>
