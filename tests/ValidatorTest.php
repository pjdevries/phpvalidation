<?php
namespace Test\Obix\Validator;

use Obix\Validator\Rule\Alphabetic;
use Obix\Validator\Rule\Choice;
use Obix\Validator\Rule\Custom;
use Obix\Validator\Rule\Email;
use Obix\Validator\Rule\Integer;
use Obix\Validator\Rule\NotNull;
use Obix\Validator\Rule\Numeric;
use Obix\Validator\Rule\StringLength;
use Obix\Validator\Rule\Url;
use Obix\Validator\Validator;
use PHPUnit\Framework\TestCase;
use Test\Obix\Validator\Mock\Request;

class ValidatorTest extends TestCase
{
    private function getValidator(): Validator
    {
        return new Validator([
            'NotNull' => new NotNull(),
            'Email' => new Email(),
            'Alphabetic' => new Alphabetic(),
            'StringLength Min' => (new StringLength())->setMinLength(3),
            'StringLength Max' => (new StringLength())->setMaxLength(11),
            'Choice' => new Choice([1, 2]),
            'Url' => new Url(),
            'IntegerValue Min' => (new Integer())->setMinValue(18),
            'IntegerValue Max' => (new Integer())->setMaxValue(33),
            'Numeric' => new Numeric(),
            'Custom is bool' => new Custom(function ($value) {
                return is_bool($value);
            })
        ]);
    }

    public function testSuccess()
    {
        $validator = $this->getValidator();

        $tests = [
            'NotNull' => 'Foo',
            'Email' => 'pieter@obix.nl',
            'Alphabetic' => 'FooBar',
            'StringLength Min' => '1234567',
            'StringLength Max' => '1234567',
            'Choice' => 2,
            'Url' => 'https://www.obix.nl',
            'IntegerValue Min' => 21,
            'IntegerValue Max' => 21,
            'Numeric' => 1.1,
            'Custom is bool' => true
        ];

        $this->assertTrue($validator->validateRequest(Request::create($tests)));
    }

    public function testFailure()
    {
        $validator = $this->getValidator();

        $tests = [
            'NotNull' => null,
            'Email' => 'pieter_at_obix.nl',
            'Alphabetic' => 'FooBar!',
            'StringLength Min' => '12',
            'StringLength Max' => '123456789012345',
            'Choice' => 3,
            'Url' => '#https://www.obix.nl',
            'IntegerValue Min' => 12,
            'IntegerValue Max' => 45,
            'Numeric' => 'abc',
            'Custom is bool' => 0
        ];

        $this->assertFalse($validator->validateRequest(Request::create($tests)));

        $errors = $validator->getErrors();
    }
}