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

use Obix\Validator\Rule\RuleBase;

class Not extends RuleBase
{
    private RuleInterface $rule;

    private string $message = '';

    public function __construct(RuleInterface $rule) {
        $this->rule = $rule;
    }

    /**
     * @inheritDoc
     */
    public function test($value, string $name, array $values): bool
    {
        $result = !$this->rule->test($value, $name, $values);

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

    public function getError(): ?string
    {
        return $this->message ? parent::getError() : $this->rule->getError();
    }
}