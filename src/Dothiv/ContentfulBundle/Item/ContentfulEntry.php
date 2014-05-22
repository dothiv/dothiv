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
 */
class ContentfulEntry implements ContentfulItem
{
    use Traits\ContentfulSys;
    use Traits\ContentfulItem;

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
     * @param string $contentTypeId
     */
    public function setContentTypeId($contentTypeId)
    {
        $this->contentTypeId = $contentTypeId;
    }

    /**
     * @return string
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulEntry: %s, v%d [%s: %s]', $this->getId(), $this->getRevision(), $this->getContentTypeId(), $this->getName());
    }
}
