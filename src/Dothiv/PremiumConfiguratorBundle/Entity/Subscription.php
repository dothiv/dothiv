<?php

namespace Dothiv\PremiumConfiguratorBundle\Entity;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Traits\CreateUpdateTime;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\ValueObject\EmailValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Premium banner configuration.
 *
 * @ORM\Entity(repositoryClass="Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class Subscription extends Entity
{
    use CreateUpdateTime;

    /**
     * The domain for this subscription
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\Domain")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=false)
     * @var Domain
     */
    protected $domain;

    /**
     * The user who created this subscription.
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=false)
     * @var User
     */
    protected $user;

    /**
     * The email the user had when creating the subscription.
     *
     * @var EmailValue
     *
     * @ORM\Column(type="string", nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Email()
     */
    protected $email;

    /**
     * The stripe token returned by the checkout.
     *
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $token;

    /**
     * The stripe customer id for this subscription.
     *
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $customer;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
     * @Serializer\Expose
     */
    protected $liveMode;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
     * @Serializer\Expose
     */
    protected $active = 0;

    /**
     * @param \Stripe_Customer
     *
     * @return self
     */
    public function activate(\Stripe_Customer $customer)
    {
        $this->active   = 1;
        $this->customer = $customer->id;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return (boolean)$this->active;
    }

    /**
     * @param Domain $domain
     *
     * @return self
     */
    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param boolean $liveMode
     *
     * @return self
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode ? 1 : 0;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isLive()
    {
        return (boolean)$this->liveMode;
    }

    /**
     * @param mixed $token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

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
     * @param EmailValue $email
     */
    public function setEmail(EmailValue $email)
    {
        $this->email = (string)$email;
    }

    /**
     * @return EmailValue
     */
    public function getEmail()
    {
        return new EmailValue($this->email);
    }

}
