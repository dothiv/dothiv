<?php

namespace Dothiv\ContentfulBundle\Item;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="contentful_contenttype__id_rev_uniq",columns={"id", "revision"})},
 *      indexes={@ORM\Index(name="contentful_contenttype__name_idx", columns={"name"})}
 * )
 * @ORM\Entity(repositoryClass="Dothiv\ContentfulBundle\Repository\DoctrineContentfulContentTypeRepository")
 */
class ContentfulContentType
{
    use Traits\ContentfulSys;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @var string
     */
    private $displayField;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $displayField
     */
    public function setDisplayField($displayField)
    {
        $this->displayField = $displayField;
    }

    /**
     * @return string
     */
    public function getDisplayField()
    {
        return $this->displayField;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulContentType: %s, v%d [%s]', $this->getId(), $this->getRevision(), $this->getName());
    }
}
