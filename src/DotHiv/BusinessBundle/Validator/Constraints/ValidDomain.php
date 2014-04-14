<?php

namespace DotHiv\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Registers the ValidDomain annotation.
 *
 * @Annotation
 * @codeCoverageIgnore
 */
class ValidDomain extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Domain name not allowed.';

    public function validatedBy()
    {
        return 'domain_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
