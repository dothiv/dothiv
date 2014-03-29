<?php

namespace DotHiv\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidDomain extends Constraint
{
    public function validatedBy()
    {
        return 'domain_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 
