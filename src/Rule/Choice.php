<?php

namespace Obix\Validator\Rule;

final class Choice extends RuleBase
{
    private string $message = '\'{{ value }}\' does not occur in list: {{ choices }}';
    private array $choices;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    public function test($value, string $name, array $values): bool
    {
        if (in_array($value, $this->choices) === false) {
            $this->setError($this->message, ['value' => $value, 'choices' => implode(', ', $this->choices)]);
            
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
