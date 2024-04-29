<?php

namespace Obix\Validator\Rule;

final class Exists extends RuleBase
{
    private string $message = 'field \'{{ field }}\' does not exist';

    public function test($value, string $name, array $values): bool
    {
        if (!array_key_exists($name, $values)) {
            $this->setError($this->message, ['field' => $name]);

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
