<?php

namespace App\Livewire\Todos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.todoapp')]
class TodoBackupRestore extends Component
{
    use WithFileUploads;

    public $backups = [];

    public function mount()
    {
        $this->loadBackups();
    }

    public function loadBackups()
    {
        $this->backups = [];
        $backupDir = 'todos/backups';

        if (Storage::disk('local')->exists($backupDir)) {
            $files = Storage::disk('local')->files($backupDir);
            foreach ($files as $file) {
                if (str_ends_with($file, '.sql')) {
                    $this->backups[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => Storage::disk('local')->size($file),
                        'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                    ];
                }
            }
            usort($this->backups, fn ($a, $b) => strcmp($b['date'], $a['date']));
        }
    }

    protected function sqlEscape($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::getPdo()->quote((string) $value);
    }

    protected function generateInserts(string $table, array $columns, $rows): string
    {
        $sql = '';
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $col) {
                $values[] = $this->sqlEscape($row[$col] ?? null);
            }
            $sql .= "INSERT INTO `{$table}` (`".implode('`, `', $columns)."`) VALUES (".implode(', ', $values).");\n";
        }

        return $sql;
    }

    public function createBackup()
    {
        try {
            $timestamp = now()->format('Y_m_d_His');

            $sql  = "-- ============================================================\n";
            $sql .= "-- TODOS FULL BACKUP\n";
            $sql .= "-- Generated: ".now()->toDateTimeString()."\n";
            $sql .= "-- ============================================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            $sql .= "-- ── todos ──\n";
            $sql .= "TRUNCATE TABLE `todos`;\n\n";

            $todos       = DB::table('todos')->get()->map(fn ($r) => (array) $r)->toArray();
            $todoColumns = [
                'id', 'title', 'description', 'is_done', 'due_at',
                'created_at', 'updated_at',
            ];
            $sql .= $this->generateInserts('todos', $todoColumns, $todos);

            $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
            $sql .= "\n-- Backup complete: ".count($todos)." todos\n";

            $filename = 'todos/backups/todos_backup_'.$timestamp.'.sql';
            Storage::disk('local')->put($filename, $sql);

            $this->loadBackups();
            session()->flash('success', 'SQL backup created: '.basename($filename).' ('.count($todos).' todos)');
        } catch (\Exception $e) {
            session()->flash('error', 'Backup failed: '.$e->getMessage());
        }
    }

    public function restoreFromFile($backupPath)
    {
        try {
            if (! Storage::disk('local')->exists($backupPath)) {
                session()->flash('error', 'Backup file not found.');

                return;
            }

            $sql = Storage::disk('local')->get($backupPath);

            if (empty($sql) || ! str_contains($sql, 'todos')) {
                session()->flash('error', 'Invalid backup file — does not contain expected todos table.');

                return;
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('todos')->truncate();

            $statements = array_filter(
                array_map('trim', explode(";\n", $sql)),
                function ($stmt) {
                    $stmt = trim($stmt);

                    return ! empty($stmt) && ! str_starts_with($stmt, '--');
                }
            );

            foreach ($statements as $statement) {
                $clean = trim($statement);
                if (empty($clean)) {
                    continue;
                }
                if (preg_match('/^INSERT/i', $clean)) {
                    DB::unprepared($clean.';');
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $todoCount = DB::table('todos')->count();

            $this->loadBackups();
            session()->flash('success', 'Restore completed! '.$todoCount.' todos restored from '.basename($backupPath));
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            session()->flash('error', 'Restore failed: '.$e->getMessage());
        }
    }

    public function deleteBackup($backupPath)
    {
        if (Storage::disk('local')->exists($backupPath)) {
            Storage::disk('local')->delete($backupPath);
            session()->flash('success', 'Backup deleted.');
            $this->loadBackups();
        }
    }

    public function render()
    {
        return view('livewire.todos.todo-backup-restore');
    }
}
