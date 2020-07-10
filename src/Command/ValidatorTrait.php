<?php

declare(strict_types=1);

namespace Devops\Command;

use UnexpectedValueException;

/**
 * Trait TransformerTrait
 * Adds custom validation and transformation to Console Input options and arguments.
 */
trait ValidatorTrait
{
    /**
     * @param mixed  $value         Generally provided from \Symfony\Component\Console\Input\InputInterface::getOption
     * @param string $error_message error message logged and displayed to user
     *
     * @return int Integer value transformed from $value
     */
    private function validateAndTransformInt($value, $error_message): int
    {
        if (!ctype_digit((string) $value) || $value < 1) {
            throw new UnexpectedValueException($error_message);
        }

        return (int) $value;
    }

    /**
     * @param mixed  $value         Generally provided from \Symfony\Component\Console\Input\InputInterface::getOption
     * @param array  $optionSet     Array of allowed values to validate against
     * @param string $error_message error message logged and displayed to user
     *
     * @return mixed Returns untransformed $value
     */
    private function validateOptionSet($value, array $optionSet, string $error_message)
    {
        if (!in_array($value, $optionSet)) {
            throw new UnexpectedValueException($error_message);
        }

        return $value;
    }
}
