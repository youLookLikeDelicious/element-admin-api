<?php
namespace App\Models\Traits;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Carbon;

trait SerializeDate
{
    /**
     * Prepare a date for array / JSON serialization.
     * 
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date instanceof DateTimeImmutable ?
            CarbonImmutable::instance($date)->toString() :
            Carbon::instance($date)->toString();
    }
}