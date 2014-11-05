<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Dothiv\BusinessBundle\Entity\Traits;

/**
 * Represents a change on an entity by an admin
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\EntityChangeRepository")
 * @ORM\Table(
 *  indexes={
 *      @ORM\Index(name="entity_change__author_idx", columns={"author"}),
 *      @ORM\Index(name="entity_change__entity_idx", columns={"entity"}),
 *      @ORM\Index(name="entity_change__identifier_idx", columns={"identifier"})
 *  }
 * )
 * @Serializer\ExclusionPolicy("all")
 */
class EntityChange implements EntityInterface
{
    use Traits\CreateTime;

    /**
     * database primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Email of the author of the change.
     *
     * @var EmailValue
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $author;

    /**
     * Entity class
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected $entity;

    /**
     * Entity identifier
     *
     * @var IdentValue
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     */
    protected $identifier;

    /**
     * List of property changes
     *
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\Type("array")
     */
    protected $changes = array();

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getId();
    }

    /**
     * @return EmailValue
     */
    public function getAuthor()
    {
        return new EmailValue($this->author);
    }

    /**
     * @param EmailValue $author
     *
     * @return self
     */
    public function setAuthor(EmailValue $author)
    {
        $this->author = (string)$author;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     *
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return IdentValue
     */
    public function getIdentifier()
    {
        return new IdentValue($this->identifier);
    }

    /**
     * @param IdentValue $identifier
     *
     * @return self
     */
    public function setIdentifier(IdentValue $identifier)
    {
        $this->identifier = (string)$identifier;
        return $this;
    }

    /**
     * @param IdentValue $property
     * @param mixed      $oldValue
     * @param mixed      $newValue
     *
     * @throws InvalidArgumentException If $oldValue is the same as $newValue
     */
    public function addChange(IdentValue $property, $oldValue, $newValue)
    {
        if ($oldValue === $newValue) {
            throw new InvalidArgumentException(
                sprintf('newValue "%s" does not differ from oldValue', $newValue)
            );
        }
        $this->changes[(string)$property] = array($oldValue, $newValue);
    }

    /**
     * @return EntityPropertyChange[]|ArrayCollection
     */
    public function getChanges()
    {
        $changes = new ArrayCollection();
        foreach ($this->changes as $property => $values) {
            $changes->set($property, new EntityPropertyChange(new IdentValue($property), $values[0], $values[1]));
        }
        return $changes;
    }

    /**
     * @param EntityPropertyChange[] $changes
     *
     * @return self
     */
    public function setChanges(array $changes)
    {
        $this->changes = array();
        foreach ($changes as $change) {
            $this->addChange($change->getProperty(), $change->getOldValue(), $change->getNewValue());
        }
        return $this;
    }
}
