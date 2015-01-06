<?php

namespace Dothiv\BusinessBundle\Repository\Traits;

use Symfony\Component\Validator\ValidatorInterface;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

trait ValidatorTrait
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param object     $entity
     * @param array|null $groups The validation groups to validate.
     *
     * @throws InvalidArgumentException if $entity is invalid
     * @return object $entity
     */
    protected function validate($entity, array $groups = null)
    {
        $errors = $this->validator->validate($entity, $groups);
        if (count($errors) != 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        return $entity;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
}
