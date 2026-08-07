<?php

namespace App\Interfaces;

use App\Models\Todo;
use Illuminate\Support\Collection;

interface TodoInterface
{
    public function all(): Collection;
    public function forDateRange(\Carbon\Carbon $start, \Carbon\Carbon $end): Collection;
    public function create(array $data): Todo;
    public function update(int $id, array $data): Todo;
    public function delete(int $id): void;
    public function toggleDone(int $id): Todo;
}
