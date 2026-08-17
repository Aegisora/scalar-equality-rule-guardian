<?php

namespace Aegisora\RuleGuardians\ScalarEqualityRule\Tests\Unit;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\ScalarEqualityRule\ScalarEqualityRuleGuardian;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class ScalarEqualityRuleGuardianTest extends TestCase
{
    private const RULE_CODE = 'scalar_equality_rule';

    private ScalarEqualityRuleGuardian $guardian;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = new ScalarEqualityRuleGuardian(
            new Guardian()
        );
    }

    /**
     * @dataProvider getEqualScalarValuesProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testSuccessfullyCheckEqual(
        $value,
        $expectedValue
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkEqual($value, $expectedValue);
    }

    /**
     * @dataProvider getEqualScalarValuesProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckNotEqualWithDefaultCustomException(
        $value,
        $expectedValue
    ): void {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->checkNotEqual($value, $expectedValue);
        } catch (GuardianValidationException $exception) {
            self::assertSame(self::RULE_CODE, $exception->getRuleCode());

            throw $exception;
        }
    }

    public static function getEqualScalarValuesProvidedData(): array
    {
        return [
            'value - null, expected - null' => [
                'value' => null,
                'expectedValue' => null,
            ],
            'value - false, expected - false' => [
                'value' => false,
                'expectedValue' => false,
            ],
            'value - true, expected - true' => [
                'value' => true,
                'expectedValue' => true,
            ],
            'value - 0, expected - 0' => [
                'value' => 0,
                'expectedValue' => 0,
            ],
            'value - 1, expected - 1' => [
                'value' => 1,
                'expectedValue' => 1,
            ],
            'value - -1, expected - -1' => [
                'value' => -1,
                'expectedValue' => -1,
            ],
            'value - 1.0, expected - 1.0' => [
                'value' => 1.0,
                'expectedValue' => 1.0,
            ],
            'value - 0.0, expected - 0.0' => [
                'value' => 0.0,
                'expectedValue' => 0.0,
            ],
            'value - empty string, expected - empty string' => [
                'value' => '',
                'expectedValue' => '',
            ],
            'value - string hello, expected - string hello' => [
                'value' => 'hello',
                'expectedValue' => 'hello',
            ],
        ];
    }

    /**
     * @dataProvider getDifferentScalarValuesProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testSuccessfullyCheckNotEqual(
        $value,
        $expectedValue
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkNotEqual($value, $expectedValue);
    }

    /**
     * @dataProvider getDifferentScalarValuesProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckEqualWithDefaultCustomException(
        $value,
        $expectedValue
    ): void {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->checkEqual($value, $expectedValue);
        } catch (GuardianValidationException $exception) {
            self::assertSame(self::RULE_CODE, $exception->getRuleCode());

            throw $exception;
        }
    }

    public static function getDifferentScalarValuesProvidedData(): array
    {
        return [
            'value - false, expected - null' => [
                'value' => false,
                'expectedValue' => null,
            ],
            'value - 0, expected - null' => [
                'value' => 0,
                'expectedValue' => null,
            ],
            'value - empty string, expected - null' => [
                'value' => '',
                'expectedValue' => null,
            ],
            'value - false, expected - true' => [
                'value' => false,
                'expectedValue' => true,
            ],
            'value - 1, expected - true' => [
                'value' => 1,
                'expectedValue' => true,
            ],
            'value - 0, expected - false' => [
                'value' => 0,
                'expectedValue' => false,
            ],
            'value - 0, expected - 1' => [
                'value' => 0,
                'expectedValue' => 1,
            ],
            'value - string 1, expected - 1' => [
                'value' => '1',
                'expectedValue' => 1,
            ],
            'value - 1, expected - 1.0' => [
                'value' => 1,
                'expectedValue' => 1.0,
            ],
            'value - false, expected - 0.0' => [
                'value' => false,
                'expectedValue' => 0.0,
            ],
            'value - NAN, expected - NAN' => [
                'value' => NAN,
                'expectedValue' => NAN,
            ],
            'value - string Hello, expected - string hello' => [
                'value' => 'Hello',
                'expectedValue' => 'hello',
            ],
            'value - 0, expected - string 0' => [
                'value' => 0,
                'expectedValue' => '0',
            ],
            'value - 1, expected - string 1' => [
                'value' => 1,
                'expectedValue' => '1',
            ],
            'value - null, expected - empty string' => [
                'value' => null,
                'expectedValue' => '',
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckEqualProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckEqual(
        $value,
        $expectedValue,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->checkEqual($value, $expectedValue, $customRuleValidationException);
    }

    public static function getFailedCheckEqualProvidedData(): array
    {
        return [
            'value - false, expected - null, custom exception - null' => [
                'value' => false,
                'expectedValue' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - false, expected - null, custom exception - not null' => [
                'value' => false,
                'expectedValue' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0, expected - null, custom exception - null' => [
                'value' => 0,
                'expectedValue' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0, expected - null, custom exception - not null' => [
                'value' => 0,
                'expectedValue' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - empty string, expected - null, custom exception - null' => [
                'value' => '',
                'expectedValue' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - empty string, expected - null, custom exception - not null' => [
                'value' => '',
                'expectedValue' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - false, expected - true, custom exception - null' => [
                'value' => false,
                'expectedValue' => true,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - false, expected - true, custom exception - not null' => [
                'value' => false,
                'expectedValue' => true,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 1, expected - true, custom exception - null' => [
                'value' => 1,
                'expectedValue' => true,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 1, expected - true, custom exception - not null' => [
                'value' => 1,
                'expectedValue' => true,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0, expected - false, custom exception - null' => [
                'value' => 0,
                'expectedValue' => false,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0, expected - false, custom exception - not null' => [
                'value' => 0,
                'expectedValue' => false,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0, expected - 1, custom exception - null' => [
                'value' => 0,
                'expectedValue' => 1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0, expected - 1, custom exception - not null' => [
                'value' => 0,
                'expectedValue' => 1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - string 1, expected - 1, custom exception - null' => [
                'value' => '1',
                'expectedValue' => 1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - string 1, expected - 1, custom exception - not null' => [
                'value' => '1',
                'expectedValue' => 1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 1, expected - 1.0, custom exception - null' => [
                'value' => 1,
                'expectedValue' => 1.0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 1, expected - 1.0, custom exception - not null' => [
                'value' => 1,
                'expectedValue' => 1.0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - false, expected - 0.0, custom exception - null' => [
                'value' => false,
                'expectedValue' => 0.0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - false, expected - 0.0, custom exception - not null' => [
                'value' => false,
                'expectedValue' => 0.0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - NAN, expected - NAN, custom exception - null' => [
                'value' => NAN,
                'expectedValue' => NAN,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - NAN, expected - NAN, custom exception - not null' => [
                'value' => NAN,
                'expectedValue' => NAN,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - string Hello, expected - string hello, custom exception - null' => [
                'value' => 'Hello',
                'expectedValue' => 'hello',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - string Hello, expected - string hello, custom exception - not null' => [
                'value' => 'Hello',
                'expectedValue' => 'hello',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0, expected - string 0, custom exception - null' => [
                'value' => 0,
                'expectedValue' => '0',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0, expected - string 0, custom exception - not null' => [
                'value' => 0,
                'expectedValue' => '0',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 1, expected - string 1, custom exception - null' => [
                'value' => 1,
                'expectedValue' => '1',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 1, expected - string 1, custom exception - not null' => [
                'value' => 1,
                'expectedValue' => '1',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - null, expected - empty string, custom exception - null' => [
                'value' => null,
                'expectedValue' => '',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - null, expected - empty string, custom exception - not null' => [
                'value' => null,
                'expectedValue' => '',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckNotEqualProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckNotEqual(
        $value,
        $expectedValue,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->checkNotEqual($value, $expectedValue, $customRuleValidationException);
    }

    public static function getFailedCheckNotEqualProvidedData(): array
    {
        return [
            'value - null, expected - null, custom exception - null' => [
                'value' => null,
                'expectedValue' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - null, expected - null, custom exception - not null' => [
                'value' => null,
                'expectedValue' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - false, expected - false, custom exception - null' => [
                'value' => false,
                'expectedValue' => false,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - false, expected - false, custom exception - not null' => [
                'value' => false,
                'expectedValue' => false,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - true, expected - true, custom exception - null' => [
                'value' => true,
                'expectedValue' => true,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - true, expected - true, custom exception - not null' => [
                'value' => true,
                'expectedValue' => true,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0, expected - 0, custom exception - null' => [
                'value' => 0,
                'expectedValue' => 0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0, expected - 0, custom exception - not null' => [
                'value' => 0,
                'expectedValue' => 0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 1, expected - 1, custom exception - null' => [
                'value' => 1,
                'expectedValue' => 1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 1, expected - 1, custom exception - not null' => [
                'value' => 1,
                'expectedValue' => 1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - -1, expected - -1, custom exception - null' => [
                'value' => -1,
                'expectedValue' => -1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - -1, expected - -1, custom exception - not null' => [
                'value' => -1,
                'expectedValue' => -1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 1.0, expected - 1.0, custom exception - null' => [
                'value' => 1.0,
                'expectedValue' => 1.0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 1.0, expected - 1.0, custom exception - not null' => [
                'value' => 1.0,
                'expectedValue' => 1.0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - 0.0, expected - 0.0, custom exception - null' => [
                'value' => 0.0,
                'expectedValue' => 0.0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - 0.0, expected - 0.0, custom exception - not null' => [
                'value' => 0.0,
                'expectedValue' => 0.0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - empty string, expected - empty string, custom exception - null' => [
                'value' => '',
                'expectedValue' => '',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - empty string, expected - empty string, custom exception - not null' => [
                'value' => '',
                'expectedValue' => '',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - string hello, expected - string hello, custom exception - null' => [
                'value' => 'hello',
                'expectedValue' => 'hello',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - string hello, expected - string hello, custom exception - not null' => [
                'value' => 'hello',
                'expectedValue' => 'hello',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckCauseValueIsNotScalarProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckEqualCauseValueIsNotScalarThrowsGuardianExecutingRuleException(
        $value,
        $expectedValue,
        ?Throwable $customRuleValidationException
    ): void {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkEqual($value, $expectedValue, $customRuleValidationException);
    }

    /**
     * @dataProvider getFailedCheckCauseValueIsNotScalarProvidedData
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testFailedCheckNotEqualCauseValueIsNotScalarThrowsGuardianExecutingRuleException(
        $value,
        $expectedValue,
        ?Throwable $customRuleValidationException
    ): void {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkNotEqual($value, $expectedValue, $customRuleValidationException);
    }

    public static function getFailedCheckCauseValueIsNotScalarProvidedData(): array
    {
        return [
            'value - scalar, expected - array' => [
                'value' => 1,
                'expectedValue' => [],
                'customRuleValidationException' => null,
            ],
            'value - scalar, expected - object' => [
                'value' => 1,
                'expectedValue' => new stdClass(),
                'customRuleValidationException' => null,
            ],
            'value - scalar, expected - resource' => [
                'value' => 1,
                'expectedValue' => tmpfile(),
                'customRuleValidationException' => new CustomRuleException(),
            ],
            'value - scalar, expected - callable' => [
                'value' => 1,
                'expectedValue' => static function () {
                },
                'customRuleValidationException' => null,
            ],
            'value - array, expected - scalar' => [
                'value' => [],
                'expectedValue' => 1,
                'customRuleValidationException' => null,
            ],
            'value - object, expected - scalar' => [
                'value' => new stdClass(),
                'expectedValue' => 1,
                'customRuleValidationException' => null,
            ],
            'value - resource, expected - scalar' => [
                'value' => tmpfile(),
                'expectedValue' => 1,
                'customRuleValidationException' => new CustomRuleException(),
            ],
            'value - callable, expected - scalar' => [
                'value' => static function () {
                },
                'expectedValue' => 1,
                'customRuleValidationException' => null,
            ],
        ];
    }

    public function testFailedCheckEqualCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new ScalarEqualityRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkEqual(null, null);
    }

    public function testFailedCheckEqualCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new ScalarEqualityRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkEqual(null, null);
    }

    public function testFailedCheckNotEqualCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new ScalarEqualityRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkNotEqual(null, null);
    }

    public function testFailedCheckNotEqualCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new ScalarEqualityRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkNotEqual(null, null);
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianThrowsExceptionOnCheck(string $expectedExceptionClass): Guardian
    {
        $guardian = $this->getGuardianMock();

        $guardian
            ->expects(self::once())
            ->method('check')
            ->willThrowException($this->createMock($expectedExceptionClass));

        return $guardian;
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianMock(): Guardian
    {
        /** @var Guardian|MockObject $mock */
        $mock = $this->createMock(Guardian::class);

        return $mock;
    }
}
