<?php

use App\Interfaces\TodoInterface;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.todoapp')] class extends Component
{
    public int $year;
    public int $month;

    public function mount()
    {
        $this->year  = now()->year;
        $this->month = now()->month;
    }

    public function prevMonth()
    {
        $c = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year  = $c->year;
        $this->month = $c->month;
    }

    public function nextMonth()
    {
        $c = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year  = $c->year;
        $this->month = $c->month;
    }

    public function render()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $todos = app(TodoInterface::class)->forDateRange($start, $end);

        $byDay = $todos->groupBy(fn ($t) => $t->due_at->format('Y-m-d'));

        $days = [];
        $cursor = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $lastCell = $end->copy()->endOfWeek(Carbon::SATURDAY);

        while ($cursor->lte($lastCell)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        return $this->view()
            ->with('days', $days)
            ->with('byDay', $byDay)
            ->with('monthLabel', $start->format('F Y'));
    }
};
?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button wire:click="prevMonth" class="btn btn-outline-secondary">&laquo; Prev</button>
        <h1 class="h4 mb-0">{{ $monthLabel }}</h1>
        <button wire:click="nextMonth" class="btn btn-outline-secondary">Next &raquo;</button>
    </div>

    <div class="d-grid gap-1 text-center fw-bold mb-1" style="grid-template-columns: repeat(7, 1fr);">
        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
            <div>{{ $d }}</div>
        @endforeach
    </div>

    <div class="d-grid gap-1" style="grid-template-columns: repeat(7, 1fr);">
        @foreach ($days as $day)
            @php $key = $day->format('Y-m-d'); @endphp
            <div class="border rounded p-2" style="min-height:110px;">
                <div class="small text-muted">{{ $day->day }}</div>
                @foreach ($byDay->get($key, []) as $todo)
                    <div class="badge {{ $todo->is_done ? 'bg-secondary' : 'bg-primary' }} d-block text-truncate mt-1" title="{{ $todo->title }}">
                        {{ $todo->title }}
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
