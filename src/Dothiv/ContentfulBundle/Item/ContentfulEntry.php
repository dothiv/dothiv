<?php

namespace Dothiv\ContentfulBundle\Item;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="contentful_entry__id_rev_uniq",columns={"id", "revision"})},
 *      indexes={@ORM\Index(name="contentful_entry__name_idx", columns={"name"})}
 * )
 * @ORM\Entity(repositoryClass="Dothiv\ContentfulBundle\Repository\DoctrineContentfulEntryRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class ContentfulEntry
{
    use Traits\ContentfulSys;
    use Traits\ContentfulItem;

    /**
     * @var ContentfulContentType
     */
    private $contentType;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @var string
     */
    private $contentTypeId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @var string
     */
    private $name;

    /**
     * @param ContentfulContentType $contentType
     */
    public function setContentType(ContentfulContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return ContentfulContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->contentTypeId = $this->getContentType()->getId();
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
     * Updates the name according to the display field of the content type.
     */
    public function updateName()
    {
        $displayField = $this->getContentType()->getDisplayField();
        $values       = array_values((array)$this->fields[$displayField]);
        $this->setName($values[0]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulEntry: %s, v%d [%s: %s]', $this->getId(), $this->getRevision(), $this->getContentType()->getName(), $this->getName());
    }
}
