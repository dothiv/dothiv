<?php


namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents an accredited registrar
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\RegistrarRepository")
 * @AssertORM\UniqueEntity("extId")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="domain__extId",columns={"extId"})})
 * @Serializer\ExclusionPolicy("all")
 */
class Registrar extends Entity
{
    use Traits\CreateUpdateTime;

    const REGISTRATION_NOFITICATION_REGULAR = "regular";

    const REGISTRATION_NOFITICATION_COBRANDED = "co-branded";

    const REGISTRATION_NOFITICATION_NONE = "none";

    /**
     * Afilias Id for the registrar, e.g. "1137-SP"
     *
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[1-9][0-9]{3}-[A-Z]{2}$/")
     * @var string
     */
    protected $extId;

    /**
     * Name of the registrar, e.g. "1&1 Internet AG"
     *
     * @ORM\Column(type="string",length=255,nullable=true)
     * @var string|null
     */
    protected $name;

    /**
     * What type of registration notification to send, can be "regular", "co-branded", "none"
     *
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Choice({"regular", "co-branded", "none"})
     * @var string
     */
    protected $registrationNotification;

    /**
     * A list of domains by this registrar
     *
     * @ORM\OneToMany(targetEntity="Domain", mappedBy="registrar")
     * @var Domain[]|ArrayCollection
     */
    protected $domains;

    public function __construct()
    {
        $this->registrationNotification = self::REGISTRATION_NOFITICATION_REGULAR;
        $this->domains                  = new ArrayCollection();
    }

    /**
     * @param string $extId
     *
     * @return self
     */
    public function setExtId($extId)
    {
        $this->extId = $extId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtId()
    {
        return $this->extId;
    }

    /**
     * @param null|string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $registrationNotification
     *
     * @return self
     */
    public function setRegistrationNotification($registrationNotification)
    {
        $this->registrationNotification = $registrationNotification;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegistrationNotification()
    {
        return $this->registrationNotification;
    }

    /**
     * @return ArrayCollection|Domain[]
     */
    public function getDomains()
    {
        return $this->domains;
    }

    public function canSendRegistrationNotification()
    {
        return in_array(
            $this->getRegistrationNotification(),
            array(self::REGISTRATION_NOFITICATION_REGULAR, self::REGISTRATION_NOFITICATION_COBRANDED)
        );
    }

    /**
     * Compares two instance of this class
     *
     * @param Registrar $registrar
     *
     * @return bool
     */
    public function equals(Registrar $registrar = null)
    {
        if (!($registrar instanceof Registrar)) {
            return false;
        }
        if ($this->getName() === $registrar->getName()
            && $this->getExtId() === $registrar->getExtId()
        ) {
            return true;
        }
        return false;
    }
}
