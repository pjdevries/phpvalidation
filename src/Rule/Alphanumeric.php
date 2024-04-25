<?php

namespace Obix\Validator\Rule;

final class Alphanumeric extends RuleBase
{
    private string $message = 'value should be of type {{ type }}';

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return true;
        }

        if (ctype_alnum($value) === false) {
            $this->setError($this->message, ['value' => $value, 'type' => 'alphanumeric']);
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