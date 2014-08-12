<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a non-profit registration put request
 */
class NonProfitRegistrationPutRequest extends NonProfitRegistrationGetRequest
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $personFirstname; // e.g.: Jill

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $personSurname; // e.g.: Jones

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $personEmail; // e.g.: jill@example.com

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $organization; // e.g.: ACME Inc.

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[a-f0-9]+$/")
     */
    public $proof; // e.g.: attachm3nt

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $about; // e.g.: ACME Stuff

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $field; // e.g.: prevention

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $postcode; // e.g.: 12345

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $locality; // e.g.: Big City

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $country; // e.g.: United States

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Url
     */
    public $website; // e.g.: http://example.com/

    /**
     * @var string
     */
    public $concept;

    /**
     * @var string
     */
    public $orgPhone;

    /**
     * @var string
     */
    public $orgFax;

    /**
     * @var string
     */
    public $personPhone;

    /**
     * @var string
     */
    public $personFax;

    /**
     * @var string
     */
    public $personPosition;

    /**
     * @var int
     * @Assert\Range(min=0,max=1)
     * @Assert\NotNull
     */
    public $forward = 0; // e.g.: 1

    /**
     * @var int
     * @Assert\Range(min=1,max=1)
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    public $terms; // e.g.: 1
}
