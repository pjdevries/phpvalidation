<?php

namespace Test\Obix\Validator;

use Obix\Validator\Rule\Alphabetic;
use Obix\Validator\Rule\Choice;
use Obix\Validator\Rule\Custom;
use Obix\Validator\Rule\Email;
use Obix\Validator\Rule\Exists;
use Obix\Validator\Rule\Integer;
use Obix\Validator\Rule\Not;
use Obix\Validator\Rule\NotNull;
use Obix\Validator\Rule\Numeric;
use Obix\Validator\Rule\RuleList;
use Obix\Validator\Rule\StringLength;
use Obix\Validator\Rule\Url;
use Obix\Validator\Validator;
use PHPUnit\Framework\TestCase;
use Test\Obix\Validator\Mock\Request;

class ValidatorTest extends TestCase
{
    public function testNotNull(): void
    {
        $this->assertTrue(
            ($validator = new Validator([
                'not null' => new NotNull(),
                'not not null' => new Not(new NotNull())
            ]))->validateRequest(
                Request::create([
                    'not null' => 'foo',
                    'not not null' => ''
                ])
            )
        );

        $this->assertFalse(
            ($validator = new Validator([
                'not null' => new NotNull(),
                'not not null' => new Not(new NotNull()),
                'null not exist' => new NotNull()
            ]))->validateRequest(
                Request::create([
                    'not null' => '',
                    'not not null' => 'foo'
                ])
            )
        );
    }

    public function testExists(): void
    {
        $this->assertTrue(
            ($validator = new Validator([
                'exists' => new Exists(),
                'not exists' => (new Not(new Exists()))->setMessage('{{ field }} does not exist')
            ]))->validateRequest(
                Request::create([
                    'exists' => 'foo'
                ])
            )
        );

        $this->assertFalse(
            ($validator = new Validator([
                'exists' => new Exists(),
                'not exists' => (new Not(new Exists()))->setMessage('field \'{{ field }}\' exists')
            ]))->validateRequest(
                Request::create([
                    'not exists' => 'foo'
                ])
            )
        );
    }

    public function testNot(): void
    {
        $this->assertTrue(
            ($validator = new Validator([
                'not true' => new Not(new Custom(fn($value) => $value === false))
            ]))->validateRequest(
                Request::create([
                    'not true' => false
                ])
            )
        );

        $this->assertFalse(
            ($validator = new Validator([
                'not true' => new Not(new Custom(fn($value) => $value === false))
            ]))->validateRequest(
                Request::create([
                    'not true' => true
                ])
            )
        );
    }

    private function getRules(): array
    {
        return [
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
        ];
    }

    private function getCollectionRules(): array
    {
        return [
            'Email or Null' => (new RuleList(new Not(new NotNull()), new Email()))
                ->setBoolOp(RuleList::OR),
            'Email or Url' => new RuleList(
                new NotNull(), (new RuleList(new Url(), new Email()))
                ->setBoolOp(RuleList::OR)
            ),
        ];
    }

    public function testSuccess()
    {
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

        $this->assertTrue((new Validator($this->getRules()))->validateRequest(Request::create($tests)));

        $collectionRules = $this->getCollectionRules();
        $testRules = [
            'Email (or Null)' => $collectionRules['Email or Null'],
            'Null (or Email)' => $collectionRules['Email or Null'],
            'Email (or Url)' => $collectionRules['Email or Url'],
            'Url (or Email)' => $collectionRules['Email or Url'],
        ];
        $collectionTests = [
            'Email (or Url)' => 'pieter@obix.nl',
            'Url (or Email)' => 'https://www.obix.nl',
        ];

        $this->assertTrue((new Validator($testRules))->validateRequest(Request::create($collectionTests)));
    }

    public function testFailure()
    {
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

        $validator = new Validator($this->getRules());
        $this->assertFalse($validator->validateRequest(Request::create($tests)));
        $errors = $validator->getErrors();

        $collectionRules = $this->getCollectionRules();
        $testRules = [
            'Email (or Null)' => $collectionRules['Email or Null'],
            'Null (or Email)' => $collectionRules['Email or Null'],
            'Email (or Url)' => $collectionRules['Email or Url'],
            'Url (or Email)' => $collectionRules['Email or Url'],
        ];
        $collectionTests = [
            'Email (or Null)' => 'bar',
            'Null (or Email)' => '',
            'Email (or Url)' => '#pieter@obix.nl',
            'Url (or Email)' => '#https://www.obix.nl',
        ];

        $validator = new Validator($testRules);
        $this->assertFalse($validator->validateRequest(Request::create($collectionTests)));
        $errors = $validator->getErrors();
    }
}