<?php

namespace Devops\Command;

use Symfony\Component\Console\Exception\InvalidOptionException;

/**
 * Trait TransformerTrait
 * Adds custom validation and transformation to Console Input options and arguments.
 * @package Devops\Command
 */
trait TransformerTrait
{
    /**
     * @param string|int $value
     * @return int
     */
    protected function validateAndTransformInt($value): int
    {
        if (!ctype_digit((string) $value) || $value < 1) {
            throw new InvalidOptionException('--limit should be a non-zero positive integer.');
        }

        return (int) $value;
    }
}
