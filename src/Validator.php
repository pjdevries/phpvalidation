<?php

namespace Obix\Validator;

use InvalidArgumentException;
use Obix\Validator\Rule\RuleInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Validator
{
    /**
     * @var array<string, array>
     */
    private array $rules = [];

    /**
     * @var array<string,string>
     */
    private array $errors = [];

    /**
     * @var array
     */
    private array $data = [];

    public function __construct(array $fieldRules)
    {
        foreach ($fieldRules as $fieldName => $rules) {
            $this->addRule($fieldName, is_array($rules) ? $rules : [$rules]);
        }
    }

    /**
     * Validate server request data.
     *
     * @param ServerRequestInterface $request The server request to validate
     * @return bool
     */
    public function validate(ServerRequestInterface $request): bool
    {
        $data = array_map(fn($value) => is_string($value) && trim($value) === '' ? null : $value,
            array_merge($request->getParsedBody(), $request->getUploadedFiles()));

        return $this->validateArray($data);
    }

    /**
     * Validate an array of data using a set of validators.
     *
     * @param array $data The array of data to be validated
     * @return bool
     */
    public function validateArray(array $data): bool
    {
        $this->data = $data;

        /**
         * @var $validators array<RuleInterface>
         */
        foreach ($this->rules as $fieldName => $rules) {
            foreach ($rules as $rule) {
                if ($rule->test($this->data[$fieldName] ?? null, $fieldName, $this->rules) === false) {
                    $this->addError($fieldName, (string)$rule->getError());
                }
            }
        }
        return !count($this->getErrors());
    }

    /**
     * @return array<string,string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Add an error for a specific field.
     *
     * @param string $field The field for which the error occurred
     * @param string $message The error message
     * @return void
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Add a validator for a specific field.
     *
     * @param string $field The field to validate
     * @param array<RuleInterface> $rules The validators to apply
     * @return void
     */
    private function addRule(string $field, array $rules): void
    {
        foreach ($rules as $rule) {
            if (!$rule instanceof RuleInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        $field . ' validator must be an instance of ValidatorInterface, "%s" given.',
                        is_object($rule) ? get_class($rule) : gettype($rule)
                    )
                );
            }

            $this->rules[$field][] = $rule;
        }
    }
}