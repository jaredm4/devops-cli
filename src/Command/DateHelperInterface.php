<?php

declare(strict_types=1);

namespace Devops\Command;

use DateTimeZone;

/**
 * Interface DateHelperInterface
 * Helps Commands display dates and times in the user's timezone and preferred format.
 * Ensure setDateTimeFormat is called in services.yaml as auto-wiring does not work for scalar values.
 */
interface DateHelperInterface
{
    public function setDateTimeZone(DateTimeZone $dateTimeZone): void;

    public function setDateTimeFormat(string $dateTimeFormat): void;
}
