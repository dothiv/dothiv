<?php

namespace DotHiv\BusinessBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidDomainValidator extends ConstraintValidator
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $allowedTLDs;

    public function __construct()
    {
        $this->allowedTLDs = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $regexp = "/^[^.]{1,67}\..{2,67}$/"; // Allow all TLDs.
        if (!$this->allowedTLDs->isEmpty()) {
            // configured TLDs
            $regexp = sprintf(
                "/^[^.]{1,67}\..(%s)$/",
                join('|', $this->allowedTLDs->toArray())
            );
        }
        if (!preg_match($regexp, $value->getName())) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }

    /**
     * @param array $allowedTLDs
     *
     * @throws \InvalidArgumentException If argument is not array.
     */
    public function setAllowedTLDs($allowedTLDs)
    {
        if (!is_array($allowedTLDs)) {
            // TODO: Convert to bundle level exception.
            throw new \InvalidArgumentException('Argument must be array.');
        }
        $this->allowedTLDs = new ArrayCollection($allowedTLDs);
    }
}
