<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a request with a domain name
 */
class DomainNameRequest extends AbstractDataModel implements DataModelInterface
{
    /**
     * Domain name
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = strtolower($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
