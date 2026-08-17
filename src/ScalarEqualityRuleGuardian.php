<?php

namespace Aegisora\RuleGuardians\ScalarEqualityRule;

use Aegisora\Guardian\Guardian;

class ScalarEqualityRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }
}
