<?php

namespace App\Rules;

use App\Models\Trip;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DoesNotOverlap implements ValidationRule
{
    public function __construct(
        protected ?string $startTime,
        protected ?string $endTime,
        protected string $modelType,
        protected ?int $ignoreTripId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->startTime) || empty($this->endTime)) {
            return;
        }

        $query = Trip::query()
            ->where($this->modelType.'_id', $value)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('scheduled_start_time', '<', $this->endTime)
                        ->where('scheduled_end_time', '>', $this->startTime);
                });
            });

        if ($this->ignoreTripId) {
            $query->where('id', '!=', $this->ignoreTripId);
        }

        if ($query->exists()) {
            $fail("The selected {$this->modelType} is already booked for an overlapping time period.");
        }
    }
}
