<?php

namespace Aegisora\RuleGuardians\ScalarEqualityRule;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\Rules\ScalarEqualityRule;
use Throwable;

class ScalarEqualityRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }

    /**
     * @param mixed $value
     * @throws GuardianExecutingRuleException
     * @throws GuardianValidationException
     * @throws Throwable
     */
    public function checkEqual(
        $value,
        $expectedValue,
        ?Throwable $exception = null
    ): void {
        $this->guardian->check($value, ScalarEqualityRule::createEqual($expectedValue), $exception);
    }
}
