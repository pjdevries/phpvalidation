<?php

namespace Obix\Validator\Rule;

final class StringLength extends RuleBase
{
    private string $invalidMessage = 'value is not a string';
    private string $minMessage = '{{ value }} must be at least {{ limit }} characters long';
    private string $maxMessage = '{{ value }} cannot be longer than {{ limit }} characters';
    private ?int $min = null;
    private ?int $max = null;

    public function test($value, string $name, array $values): bool
    {
        if (null === $value) {
            return true;
        }

        if (!is_string($value)) {
            $this->setError($this->invalidMessage, ['value' => $value]);
            return false;
        }

        if (is_int($this->min) && strlen($value) < $this->min) {
            $this->setError($this->minMessage, ['value' => $value, 'limit' => $this->min]);
            return false;
        }

        if (is_int($this->max) && strlen($value) > $this->max) {
            $this->setError($this->maxMessage, ['value' => $value, 'limit' => $this->max]);
            return false;
        }

        return true;
    }

    public function setInvalidMessage(string $invalidMessage): self
    {
        $this->invalidMessage = $invalidMessage;

        return $this;
    }

    public function setMinMessage(string $minMessage): self
    {
        $this->minMessage = $minMessage;

        return $this;
    }

    public function setMaxMessage(string $maxMessage): self
    {
        $this->maxMessage = $maxMessage;

        return $this;
    }

    public function setMinLength(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function setMaxLength(int $max): self
    {
        $this->max = $max;

        return $this;
    }
}
