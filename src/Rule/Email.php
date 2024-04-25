<?php

namespace Obix\Validator\Rule;

final class Email extends RuleBase
{
    /**
     * @var string
     */
    private string $message = '{{ value }} is not a valid email address';

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) === false) {
            $this->setError($this->message, ['value' => $value]);
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->setError($this->message, ['value' => $value]);
            return false;
        }

        return true;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
