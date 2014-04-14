<?php

namespace DotHiv\BusinessBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use DotHiv\BusinessBundle\Entity\Domain;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validate the TLD of domain entities.
 *
 * Validates entites to have a $name property which is a domain with an
 * allowed TLD. Allowed TLDs are configured via dot_hiv_business.allowd_tlds
 * configuration option.
 */
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
        /* @var Domain $value */
        /* @var ValidDomain $constraint */
        $regexp = "/^[^.]{1,67}\.[^\.]{2,67}$/"; // Allow all TLDs.
        if (!$this->allowedTLDs->isEmpty()) {
            // Allow configured TLDs.
            $regexp = sprintf(
                "/^[^.]{1,67}\.(%s)$/",
                join('|', $this->allowedTLDs->toArray())
            );
        }
        if (!preg_match($regexp, strtoupper($value->getName()))) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value->getName()));
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
            $allowedTLDs = array($allowedTLDs);
        }
        $tlds = array_map(function ($tld) {
            return strtoupper($tld);
        }, $allowedTLDs);
        foreach ($tlds as $tld) {
            if (!preg_match('/^[^\.]{2,67}$/', $tld)) {
                throw new \InvalidArgumentException(sprintf('Invalid TLD specification: "%s".', $tld));
            }
        }
        $this->allowedTLDs = new ArrayCollection($tlds);
    }
}
