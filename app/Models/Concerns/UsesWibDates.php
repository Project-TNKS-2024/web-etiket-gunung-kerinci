<?php

namespace App\Models\Concerns;

use DateTimeInterface;
use Illuminate\Support\Carbon;

trait UsesWibDates
{
    protected function asDateTime($value)
    {
        // A database date without an offset is always a WIB wall time.
        // Explicit Z/+offset inputs still retain their original instant.
        if (is_string($value) && !is_numeric($value)) {
            return Carbon::parse($value, config('app.timezone'));
        }

        return parent::asDateTime($value);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::instance($date)->setTimezone(config('app.timezone'))->toIso8601String();
    }

    public function fromDateTime($value)
    {
        return empty($value) ? $value : $this->asDateTime($value)
            ->setTimezone(config('app.timezone'))->format($this->getDateFormat());
    }
}
