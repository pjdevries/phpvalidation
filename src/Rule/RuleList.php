<?php
/**
 * @package     phpvalidation
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2024+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Obix\Validator\Rule;

class RuleList extends RuleBase
{
    const AND = 1;
    const OR = 2;

    /**
     * @var array<RuleInterface>
     */
    private array $rules = [];

    private int $boolOp = self::AND;

    private string $message = 'value does not meet validation criteria';

    public function __construct(RuleInterface ...$rules)
    {
        $this->addRules(...$rules);
    }

    public function setBoolOp(int $boolOp): self {
        $this->boolOp = $boolOp;

        return $this;
    }

    public function addRules(RuleInterface ...$rules)
    {
        $this->rules = [...$this->rules, ...$rules];
    }

    public function test($value, string $name, array $values): bool
    {
        $result = $this->boolOp === self::AND;

        foreach ($this->rules as $rule) {
            if ($this->boolOp === self::AND) {
                $result = $result && $rule->test($value, $name, $values);
            } else {
                $result = $result || $rule->test($value, $name, $values);
            }

            if (($this->boolOp === self::AND && !$result) || ($this->boolOp === self::OR && $result)) {
                break;
            }
        }

        if (!$result) {
            $this->setError($this->message, ['value' => $value, 'field' => $name]);
        }

        return $result;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}