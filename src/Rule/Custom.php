<?php

namespace Obix\Validator\Rule;

final class Custom extends RuleBase
{
    private string $message = '"{{ value }}" is not valid';
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $validate)
    {
        $this->callable = $validate;
    }

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return false;
        }

        $callable = $this->callable;

        if ($callable($value) === false) {
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
