<?php

namespace Dothiv\ContentfulBundle\Item;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="contentful_contenttype__space_id_rev_uniq",columns={"spaceId", "id", "revision"})},
 *      indexes={
 *          @ORM\Index(name="contentful_contenttype__name_idx", columns={"name"}),
 *          @ORM\Index(name="contentful_contenttype__spaceId_idx", columns={"spaceId"})
 *      }
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
     * Updates the name according to the display field of the content type.
     *
     * @param ContentfulEntry $entry
     */
    public function updateEntryName(ContentfulEntry $entry)
    {
        $displayField = $this->getDisplayField();
        $values       = array_values((array)$entry->{$displayField});
        $entry->setName($values[0]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulContentType: %s@%s, v%d [%s]', $this->getId(), $this->getSpaceId(), $this->getRevision(), $this->getName());
    }
}
