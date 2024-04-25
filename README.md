# PHP Validation

PHP Validation is a simple and extensible PHP validation library. It is based
on https://github.com/devcoder-xyz/php-validator/

## Installation

You can install this library via [Composer](https://getcomposer.org/). Ensure your project meets the minimum PHP version
requirement of 7.4.

```bash
composer require devcoder-xyz/php-validator
```

## Requirements

- PHP version 7.4 or higher
- Required package for PSR-7 HTTP Message (e.g., `guzzlehttp/psr7`)

## Usage

The PHP Validation library enables you to validate data in a simple and flexible manner using pre-configured
validation rules. Here are some usage examples:

### Example 1: Email Address Validation

Validate an email address to ensure it is not null and matches the standard email format.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Email;

// Instantiate Validation object for email validation
$validator = new Validator([
    'email' => [new NotNull(), new Email()]
]);

// Example data array
$data = ['email' => 'john.doe@example.com'];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Email is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['email']);
}
```

### Example 2: Age Validation

Validate the age to ensure it is a non-null integer and is 18 or older.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Integer;

// Instantiate Validation object for age validation
$validator = new Validator([
    'age' => [new NotNull(), new Integer(['min' => 18])]
]);

// Example data array
$data = ['age' => 25];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Age is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['age']);
}
```

### Additional Examples

Let's explore more examples covering various validators:

#### Username Validation

Ensure that a username is not null, has a minimum length of 3 characters, and contains only alphanumeric characters.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Alphanumeric;
use Obix\Validator\Rules\StringLength;

// Instantiate Validation object for username validation
$validator = new Validator([
    'username' => [new NotNull(), new StringLength(['min' => 3]), new Alphanumeric()]
]);

// Example data array
$data = ['username' => 'john_doe123'];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Username is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['username']);
}
```

#### URL Validation

Validate a URL to ensure it is not null and is a valid URL format.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Url;

// Instantiate Validation object for URL validation
$validator = new Validator([
    'website' => [new NotNull(), new Url()]
]);

// Example data array
$data = ['website' => 'https://example.com'];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Website URL is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['website']);
}
```

#### Numeric Value Validation

Validate a numeric value to ensure it is not null and represents a valid numeric value.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Numeric;

// Instantiate Validation object for numeric value validation
$validator = new Validator([
    'price' => [new NotNull(), new Numeric()]
]);

// Example data array
$data = ['price' => 99.99];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Price is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['price']);
}
```

#### Custom Validation Rule

Implement a custom validation rule using a callback function.

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Custom;

// Custom validation function to check if the value is a boolean
$isBoolean = function ($value) {
    return is_bool($value);
};

// Instantiate Validation object with a custom validation rule
$validator = new Validator([
    'active' => [new NotNull(), new Custom($isBoolean)]
]);

// Example data array
$data = ['active' => true];

// Validate the data
if ($validator->validate($data) === true) {
    echo "Value is valid!";
} else {
    $errors = $validator->getErrors();
    echo "Validation errors: " . implode(", ", $errors['active']);
}
```

## Using `validate(ServerRequestInterface $request)` Method

The `Validation` class provides a convenient method `validate(ServerRequestInterface $request)` to validate data
extracted from a `\Psr\Http\Message\ServerRequestInterface` object. This method simplifies the process of validating
request data typically received in a web application.

### Example 1: Validating User Registration Form

Suppose you have a user registration form with fields like `username`, `email`, `password`, and `age`. Here's how you
can use the `validate` method to validate this form data:

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Email;
use Obix\Validator\Rules\Integer;

// Define validation rules for each field
$validator = new Validator([
    'username' => [new NotNull()],
    'email' => [new NotNull(), new Email()],
    'password' => [new NotNull()],
    'age' => [new NotNull(), new Integer()]
]);

// Assume $request is the \Psr\Http\Message\ServerRequestInterface object containing form data
if ($validator->validateRequest($request) === true) {
    // Validation passed, retrieve validated data
    $validatedData = $validator->getData();
    // Process registration logic here (e.g., save to database)
    // $username = $validatedData['username'];
    // $email = $validatedData['email'];
    // $password = $validatedData['password'];
    // $age = $validatedData['age'];
    echo "Registration successful!";
} else {
    // Validation failed, retrieve validation errors
    $errors = $validator->getErrors();
    // Handle validation errors (e.g., display error messages to the user)
    echo "Validation errors: " . implode(", ", $errors);
}
```

In this example:

- We instantiate a `Validation` object with validation rules defined for each
  field (`username`, `email`, `password`, `age`).
- We call the `validate` method with the `$request` object containing form data.
- If validation passes (`validate` method returns `true`), we retrieve the validated data using `$validator->getData()`
  and proceed with the registration logic.
- If validation fails, we retrieve the validation errors using `$validator->getErrors()` and handle them accordingly.

### Example 2: Validating API Input Data

Consider validating input data received via an API endpoint. Here's how you can use the `validate` method in this
context:

```php
use Obix\Validator\Validator;
use Obix\Validator\Rules\NotNull;
use Obix\Validator\Rules\Numeric;

// Define validation rules for API input data
$validator = new Validator([
    'product_id' => [new NotNull(), new Numeric()],
    'quantity' => [new NotNull(), new Numeric()]
]);

// Assume $request is the \Psr\Http\Message\ServerRequestInterface object containing API input data
if ($validator->validateRequest($request) === true) {
    // Validation passed, proceed with processing API request
    $validatedData = $validator->getData();
    // Extract validated data
    $productId = $validatedData['product_id'];
    $quantity = $validatedData['quantity'];
    echo "API request validated successfully!";
} else {
    // Validation failed, retrieve validation errors
    $errors = $validator->getErrors();
    // Handle validation errors (e.g., return error response to the client)
    echo "Validation errors: " . implode(", ", $errors);
}
```

In this example:

- We define validation rules for `product_id` and `quantity`.
- We call the `validate` method with the `$request` object containing API input data.
- If validation passes, we retrieve the validated data using `$validator->getData()` and proceed with processing the API
  request.
- If validation fails, we retrieve the validation errors using `$validator->getErrors()` and handle them appropriately.

---

### Additional Features

- **Simple Interface**: Easily define validation rules using a straightforward interface.
- **Extensible**: Extend the library with custom validation rules by implementing the `ValidatorInterface`.
- **Error Handling**: Retrieve detailed validation errors for each field.

---

## License

This library is open-source software licensed under the [MIT license](https://mit-license.org/) and [GNU Library General Public License, version 2.0](https://www.gnu.org/licenses/old-licenses/lgpl-2.0.html).

