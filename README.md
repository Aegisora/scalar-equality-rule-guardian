# Aegisora Scalar Equality Rule Guardian

[![Latest Version](https://img.shields.io/packagist/v/aegisora/scalar-equality-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/scalar-equality-rule-guardian)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/scalar-equality-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/scalar-equality-rule-guardian)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Scalar Equality Rule Guardian provides a simple shortcut for scalar equality validation using `aegisora/guardian` and `aegisora/scalar-equality-rule`.

It is designed for cases where you want to quickly check whether a scalar value **is** (or **is not**) strictly equal to an expected scalar value, without manually building a `ScalarEqualityRule` and a validation pipeline by hand.

This package is built on top of:

* [aegisora/guardian](https://github.com/Aegisora/guardian)
* [aegisora/scalar-equality-rule](https://github.com/Aegisora/scalar-equality-rule)

---

## ✨ Features

* 🔹 Simple shortcut API for `ScalarEqualityRule`
* 🔹 Validates strict equality (`===`) via `checkEqual()`
* 🔹 Validates strict inequality (`!==`) via `checkNotEqual()`
* 🔹 Works with any scalar value (`int`, `float`, `string`, `bool`) and `null`
* 🔹 Uses `aegisora/guardian` internally
* 🔹 Uses `aegisora/scalar-equality-rule` internally
* 🔹 Supports a custom validation exception
* 🔹 Keeps rule execution errors separated from validation errors
* 🔹 Fully compatible with the Aegisora ecosystem
* 🔹 Ready to use out of the box

---

## 📦 Installation

```bash
composer require aegisora/scalar-equality-rule-guardian
```

---

## 🚀 Core Concept

This package wraps the common scalar equality validation flow:

```php
$guardian->check(
    $value,
    ScalarEqualityRule::createEqual($expectedValue),
    new ValuesNotEqualException()
);
```

into a dedicated shortcut class:

```php
$scalarEqualityRuleGuardian->checkEqual($value, $expectedValue, new ValuesNotEqualException());
```

Instead of manually creating a `ScalarEqualityRule` and passing it to `Guardian`, you can use `ScalarEqualityRuleGuardian` directly.

---

## 🏗️ Basic Usage

```php
use Aegisora\Guardian\Guardian;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\RuleGuardians\ScalarEqualityRule\ScalarEqualityRuleGuardian;

$guardian = new Guardian();

$scalarEqualityRuleGuardian = new ScalarEqualityRuleGuardian($guardian);

try {
    $scalarEqualityRuleGuardian->checkEqual($value, $expectedValue);
    // $value is strictly equal to $expectedValue
} catch (GuardianValidationException $exception) {
    // $value is not equal to $expectedValue
}
```

`checkEqual()` **passes when** `$value === $expectedValue`, and **fails otherwise**.
`checkNotEqual()` is the exact opposite — it **passes when** `$value !== $expectedValue`.

---

## ✅ How scalar equality works

Comparison is **strict** (`===`), so both type and value must match:

```php
$scalarEqualityRuleGuardian->checkEqual(1, 1);       // passes
$scalarEqualityRuleGuardian->checkEqual(1, '1');     // fails (int vs string)
$scalarEqualityRuleGuardian->checkEqual(1.0, 1);     // fails (float vs int)
$scalarEqualityRuleGuardian->checkEqual(null, null); // passes
```

And the inverse for `checkNotEqual()`:

```php
$scalarEqualityRuleGuardian->checkNotEqual(1, '1');  // passes
$scalarEqualityRuleGuardian->checkNotEqual(1, 1);    // fails
```

Both the value and the expected value must be **scalar or `null`**. Passing an `array`, `object`, `resource` or `callable` cannot be executed as a scalar comparison and throws a [rule execution exception](#-exceptions).

---

## 🧩 Usage with Custom Exception

You may provide your own exception for validation failure. It must be the **last** argument.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\ScalarEqualityRule\ScalarEqualityRuleGuardian;
use App\Exceptions\ValuesNotEqualException;

$guardian = new Guardian();

$scalarEqualityRuleGuardian = new ScalarEqualityRuleGuardian($guardian);

$scalarEqualityRuleGuardian->checkEqual(
    $value,
    $expectedValue,
    new ValuesNotEqualException()
);
```

If the values are not equal, the provided exception will be thrown instead of `GuardianValidationException`.

This is useful when validation errors should have domain-specific meaning.

---

## 🧪 Example in Application Service

```php
use Aegisora\RuleGuardians\ScalarEqualityRule\ScalarEqualityRuleGuardian;
use App\Exceptions\InvalidConfirmationTokenException;

final class ConfirmationService
{
    private ScalarEqualityRuleGuardian $scalarEqualityRuleGuardian;

    public function __construct(
        ScalarEqualityRuleGuardian $scalarEqualityRuleGuardian
    ) {
        $this->scalarEqualityRuleGuardian = $scalarEqualityRuleGuardian;
    }

    public function confirm(string $providedToken, string $expectedToken): void
    {
        $this->scalarEqualityRuleGuardian->checkEqual(
            $providedToken,
            $expectedToken,
            new InvalidConfirmationTokenException()
        );

        // business logic for a confirmed action
    }
}
```

---

## 🚨 Exceptions

The package raises two kinds of validation-related exceptions, both delegated to `Guardian` (the outcome of running the rule):

### `GuardianValidationException`

Thrown when validation fails and no custom exception is provided.

The rule code for a failed scalar equality check is `scalar_equality_rule`.

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;

try {
    $scalarEqualityRuleGuardian->checkEqual($value, $expectedValue);
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "scalar_equality_rule"
}
```

### Custom exception

When a custom exception is passed as the last argument, it is thrown instead of `GuardianValidationException` on validation failure.

```php
use App\Exceptions\ValuesNotEqualException;

try {
    $scalarEqualityRuleGuardian->checkEqual($value, $expectedValue, new ValuesNotEqualException());
} catch (ValuesNotEqualException $exception) {
    // domain-specific handling
}
```

### `GuardianExecutingRuleException`

Thrown when the underlying rule fails to execute (raises a `RuleException` during validation), as opposed to simply reporting an invalid result.

This happens when the value or the expected value is neither scalar nor `null` (e.g. an `array`, `object`, `resource` or `callable`).

```php
use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;

try {
    $scalarEqualityRuleGuardian->checkEqual([], 1);
} catch (GuardianExecutingRuleException $exception) {
    // the rule could not be executed
}
```

---

## 🧩 API

### `ScalarEqualityRuleGuardian::checkEqual()`

```php
/**
 * @param mixed $value
 * @param mixed $expectedValue
 * @throws GuardianExecutingRuleException
 * @throws GuardianValidationException
 * @throws \Throwable
 */
public function checkEqual($value, $expectedValue, ?\Throwable $exception = null): void
```

Validates that `$value` is **strictly equal** (`===`) to `$expectedValue`.

### `ScalarEqualityRuleGuardian::checkNotEqual()`

```php
/**
 * @param mixed $value
 * @param mixed $expectedValue
 * @throws GuardianExecutingRuleException
 * @throws GuardianValidationException
 * @throws \Throwable
 */
public function checkNotEqual($value, $expectedValue, ?\Throwable $exception = null): void
```

Validates that `$value` is **strictly not equal** (`!==`) to `$expectedValue`.

Arguments (both methods):

* `$value` — the scalar (or `null`) value to validate
* `$expectedValue` — the scalar (or `null`) value to compare against
* `$exception` — an optional custom `\Throwable` to be thrown on validation failure

Both methods return `void`. They communicate results through exceptions only — they return nothing on success and throw on failure:

* `GuardianValidationException` — the equality/inequality check failed and no custom exception was provided
* the provided custom exception — the check failed and a custom exception was passed
* `GuardianExecutingRuleException` — the value or expected value is not scalar (and not `null`), so the rule could not be executed

---

## 🏛️ Architecture

This package is a small shortcut layer over the Aegisora validation pipeline.

Flow:

1. `ScalarEqualityRuleGuardian::checkEqual()` / `checkNotEqual()` is called with a value, an expected value and an optional exception
2. A `ScalarEqualityRule` is created (`createEqual()` or `createNotEqual()`)
3. `Guardian` executes the rule against the value
4. If the check passes, execution continues normally
5. If the check fails, the custom exception or `GuardianValidationException` is thrown
6. If the value is not scalar (and not `null`), `GuardianExecutingRuleException` is thrown

Internal flow:

```
value + expectedValue → ScalarEqualityRuleGuardian → Guardian → ScalarEqualityRule → Result → Exception
```

---

## 🔗 Related Packages

* [aegisora/guardian](https://github.com/Aegisora/guardian) — validation execution orchestrator
* [aegisora/scalar-equality-rule](https://github.com/Aegisora/scalar-equality-rule) — scalar equality rule
* [aegisora/rule-contract](https://github.com/Aegisora/rule-contract) — base rule contract and validation result architecture

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the LICENSE for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated!. See the CONTRIBUTING for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
