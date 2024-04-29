<?php

namespace Obix\Validator\Rule;

final class Url extends RuleBase
{
    /**
     * @var string
     */
    private string $message = '{{ value }} is not a valid URL.';

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return false;
        }

        if (!is_string($value)) {
            $this->setError($this->message, ['value' => $value]);
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
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
