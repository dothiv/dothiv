<?php

namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DomainRegisteredMailer
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var ClockValue
     */
    private $clock;

    /**
     * @param ContentMailerInterface    $contentMailer
     * @param RouterInterface           $router
     * @param UserServiceInterface      $userService
     * @param DomainRepositoryInterface $domainRepo
     * @param ClockValue                $clock
     */
    public function __construct(
        ContentMailerInterface $contentMailer,
        RouterInterface $router,
        UserServiceInterface $userService,
        DomainRepositoryInterface $domainRepo,
        ClockValue $clock
    )
    {
        $this->router        = $router;
        $this->userService   = $userService;
        $this->contentMailer = $contentMailer;
        $this->domainRepo    = $domainRepo;
        $this->clock         = $clock;
    }

    /**
     * @param Domain $domain
     *
     * @return void
     */
    public function sendRegisteredDomainMail(Domain $domain)
    {
        $registrar = $domain->getRegistrar();
        if (!$registrar->canSendRegistrationNotification()) {
            return;
        }
        $email     = $domain->getOwnerEmail();
        $firstname = null;
        $surname   = null;
        $owner     = $domain->getOwnerName();
        if ($pos = strrpos($owner, ' ')) {
            $firstname = trim(substr($owner, 0, $pos));
            $surname   = trim(substr($owner, $pos));
        } else {
            $surname = $owner;
        }
        $user      = $this->userService->getOrCreateUser($email, $firstname, $surname);
        $userToken = $this->userService->createUserToken($user, new IdentValue('domainclaim'), 86400 * 14);

        $link = $this->router->generate(
            'dothiv_charity_account_index',
            array('locale' => 'en'),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link .= sprintf('#!/auth/%s/%s', $user->getHandle(), $userToken->getBearerToken());

        $data = array(
            'domainName'     => $domain->getName(),
            'ownerName'      => $domain->getOwnerName(),
            'ownerEmail'     => $domain->getOwnerEmail(),
            'loginLink'      => $link,
            'claimToken'     => $domain->getToken(),
            'registrar'      => $registrar->getName(),
            'registrarExtId' => $registrar->getExtId(),
        );

        $template = $registrar->getRegistrationNotification() == Registrar::REGISTRATION_NOFITICATION_COBRANDED ? 'domain.registered.cobranded' : 'domain.registered';

        $this->contentMailer->sendContentTemplateMail($template, 'en', $domain->getOwnerEmail(), $domain->getOwnerName(), $data);

        $domain->setTokenSent($this->clock->getNow());
        $this->domainRepo->persist($domain)->flush();
    }

    public function onDomainRegistered(DomainEvent $event)
    {
        $this->sendRegisteredDomainMail($event->getDomain());
    }
}
