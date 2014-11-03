<?php


namespace Dothiv\BusinessBundle\Entity;

interface EntityInterface
{
    /**
     * returns gets the database primary key
     *
     * @return mixed
     */
    public function getId();
    
    /**
     * Returns the public identifier for this entity
     * 
     * @return mixed
     */
    public function getPublicId();
} 
