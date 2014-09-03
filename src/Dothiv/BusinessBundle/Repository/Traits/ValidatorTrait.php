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
     * @param object $entity
     *
     * @throws InvalidArgumentException if $entity is invalid
     * @return object $entity
     */
    protected function validate($entity)
    {
        $errors = $this->validator->validate($entity);
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
