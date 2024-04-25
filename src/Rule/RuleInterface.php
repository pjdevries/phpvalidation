<?php

namespace Obix\Validator\Rule;

/**
 * Interface for rules
 */
interface RuleInterface
{
    /**
     * Validate a given value
     *
     * @param mixed $value The value to validate
     * @return bool Whether the value is valid or not
     */
    public function test($value, string $name, array $values): bool;

    /**
     * Get the error message if validation fails
     *
     * @return string|null The error message or null if no error
     */
    public function getError(): ?string;
}