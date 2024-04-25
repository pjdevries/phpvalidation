<?php

namespace Obix\Validator\Rule;

final class NotNull extends RuleBase
{
    private string $message = 'value should not be null';

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
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
