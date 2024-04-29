<?php

namespace Obix\Validator\Rule;

final class Integer extends RuleBase
{
    private string $invalidMessage = 'value should be of type {{ type }}';
    private string $minMessage = '{{ value }} should be {{ limit }} or more';
    private string $maxMessage = '{{ value }} should be {{ limit }} or less';
    private ?int $min = null;
    private ?int $max = null;

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return false;
        }

        if (ctype_digit(strval($value)) === false) {
            $this->setError($this->invalidMessage, ['value' => $value, 'type' => 'integer']);
            return false;
        }

        if (is_int($this->min) && $value < $this->min) {
            $this->setError($this->minMessage, ['value' => $value, 'limit' => $this->min]);
            return false;
        }

        if (is_int($this->max) && $value > $this->max) {
            $this->setError($this->maxMessage, ['value' => $value, 'limit' => $this->max]);
            return false;
        }

        return true;
    }

    public function invalidMessage(string $invalidMessage): self
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

    public function setMinValue(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function setMaxValue(int $max): self
    {
        $this->max = $max;

        return $this;
    }
}
