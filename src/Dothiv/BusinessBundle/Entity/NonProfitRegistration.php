<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\W3CDateTimeValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents a non-profit registration
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class NonProfitRegistration extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The user that create the attachment
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $user;

    /**
     * FQDN, no trailing dot.
     *
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @Serializer\Expose
     * @var string
     */
    protected $domain;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $personFirstname; // e.g.: Jill

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $personSurname; // e.g.: Jones

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Serializer\Expose
     */
    protected $personPosition; // e.g.: CEO

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $personEmail; // e.g.: jill@example.com

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Serializer\Expose
     */
    protected $personPhone;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Serializer\Expose
     */
    protected $personFax;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $organization; // e.g.: ACME Inc.

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Serializer\Expose
     */
    protected $orgPhone;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Serializer\Expose
     */
    protected $orgFax;

    /**
     * The attached proof
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumn()
     * @var Attachment
     */
    protected $proof;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="text")
     * @Serializer\Expose
     */
    protected $about; // e.g.: ACME Stuff

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     */
    protected $concept;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $field; // e.g.: prevention

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $postcode; // e.g.: 12345

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $locality; // e.g.: Big City

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $country; // e.g.: United States

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Url
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $website; // e.g.: http://example.com/

    /**
     * @var int
     * @Assert\Range(min=0,max=1)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    protected $forward; // e.g.: 1

    /**
     * Timestamp of when the receipt confirmation has been sent.
     *
     * @var \DateTime $receiptSent
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receiptSent;

    /**
     * This application has been approved
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $approved;

    /**
     * The domain for this application has been registered
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registered;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = strtolower($domain);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns the UTF8 representation of the domain.
     *
     * @return string
     */
    public function getDomainUTF8()
    {
        return idn_to_utf8($this->getDomain());
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param int $forward
     */
    public function setForward($forward)
    {
        $this->forward = $forward;
    }

    /**
     * @return int
     */
    public function getForward()
    {
        return $this->forward;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $orgFax
     */
    public function setOrgFax($orgFax)
    {
        $this->orgFax = $orgFax;
    }

    /**
     * @return string
     */
    public function getOrgFax()
    {
        return $this->orgFax;
    }

    /**
     * @param string $orgPhone
     */
    public function setOrgPhone($orgPhone)
    {
        $this->orgPhone = $orgPhone;
    }

    /**
     * @return string
     */
    public function getOrgPhone()
    {
        return $this->orgPhone;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $personEmail
     */
    public function setPersonEmail($personEmail)
    {
        $this->personEmail = $personEmail;
    }

    /**
     * @return string
     */
    public function getPersonEmail()
    {
        return $this->personEmail;
    }

    /**
     * @param string $personFax
     */
    public function setPersonFax($personFax)
    {
        $this->personFax = $personFax;
    }

    /**
     * @return string
     */
    public function getPersonFax()
    {
        return $this->personFax;
    }

    /**
     * @param string $personFirstname
     */
    public function setPersonFirstname($personFirstname)
    {
        $this->personFirstname = $personFirstname;
    }

    /**
     * @return string
     */
    public function getPersonFirstname()
    {
        return $this->personFirstname;
    }

    /**
     * @param string $personPhone
     */
    public function setPersonPhone($personPhone)
    {
        $this->personPhone = $personPhone;
    }

    /**
     * @return string
     */
    public function getPersonPhone()
    {
        return $this->personPhone;
    }

    /**
     * @param string $personSurname
     */
    public function setPersonSurname($personSurname)
    {
        $this->personSurname = $personSurname;
    }

    /**
     * @return string
     */
    public function getPersonSurname()
    {
        return $this->personSurname;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param \Dothiv\BusinessBundle\Entity\Attachment $proof
     */
    public function setProof($proof)
    {
        $this->proof = $proof;
    }

    /**
     * @return \Dothiv\BusinessBundle\Entity\Attachment
     */
    public function getProof()
    {
        return $this->proof;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param \DateTime $receiptSent
     */
    public function setReceiptSent(\DateTime $receiptSent)
    {
        $this->receiptSent = $receiptSent;
    }

    /**
     * @return \DateTime
     */
    public function getReceiptSent()
    {
        return $this->receiptSent;
    }

    /**
     * @param string $personPosition
     *
     * @return self
     */
    public function setPersonPosition($personPosition)
    {
        $this->personPosition = $personPosition;
        return $this;
    }

    /**
     * @return string
     */
    public function getPersonPosition()
    {
        return $this->personPosition;
    }

    /**
     * @param string $concept
     *
     * @return self
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @return W3CDateTimeValue|null
     */
    public function getApproved()
    {
        return !$this->approved ? null : new W3CDateTimeValue($this->approved);
    }

    /**
     * @param W3CDateTimeValue|null $when
     *
     * @return self
     */
    public function setApproved(W3CDateTimeValue $when = null)
    {
        $this->approved = !$when ? null : new \DateTime($when->toScalar());
    }

    /**
     * @return W3CDateTimeValue|null
     */
    public function getRegistered()
    {
        return !$this->registered ? null : new W3CDateTimeValue($this->registered);
    }

    /**
     * @param W3CDateTimeValue|null $when
     *
     * @return self
     */
    public function setRegistered(W3CDateTimeValue $when = null)
    {
        $this->registered = !$when ? null : new \DateTime($when->toScalar());
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getDomain();
    }
}
