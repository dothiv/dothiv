<?php


namespace Dothiv\UserReminderBundle\Entity;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\Traits\CreateTime;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Stores user notifications for entites.
 *
 * @ORM\Entity(repositoryClass="Dothiv\UserReminderBundle\Repository\UserReminderRepository")
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index(name="userreminder__ident_idx", columns={"ident"})
 *      }
 * )
 * @Serializer\ExclusionPolicy("all")
 */
class UserReminder extends Entity
{
    use CreateTime;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $ident;

    /**
     * Type of the notification
     *
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $type;

    /**
     * @return IdentValue
     */
    public function getIdent()
    {
        return new IdentValue($this->ident);
    }

    /**
     * @param EntityInterface $item
     *
     * @return self
     */
    public function setIdent(EntityInterface $item)
    {
        $this->ident = IdentValue::create($item->getPublicId())->toScalar();
        return $this;
    }

    /**
     * @return IdentValue
     */
    public function getType()
    {
        return new IdentValue($this->type);
    }

    /**
     * @param IdentValue $type
     *
     * @return self
     */
    public function setType(IdentValue $type)
    {
        $this->type = $type->toScalar();
        return $this;
    }
}
