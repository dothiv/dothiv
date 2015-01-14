<?php


namespace Dothiv\CharityWebsiteBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Entity\DomainNotification;
use Dothiv\CharityWebsiteBundle\Exception\EntityNotFoundException;
use Dothiv\CharityWebsiteBundle\Repository\DomainNotificationRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class ClickCounterConfigurationService implements SendClickCounterConfigurationServiceInterface
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var DomainNotificationRepositoryInterface
     */
    private $domainConfigNotificationRepo;

    /**
     * @var ContentMailerInterface
     */
    private $mailer;

    public function __construct(
        DomainRepositoryInterface $domainRepo,
        DomainNotificationRepositoryInterface $domainConfigNotificationRepo,
        ContentMailerInterface $mailer
    )
    {
        $this->domainRepo                   = $domainRepo;
        $this->domainConfigNotificationRepo = $domainConfigNotificationRepo;
        $this->mailer                       = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfiguration(HivDomainValue $domain)
    {
        /** @var Domain $entity */
        $entity = $this->domainRepo->getDomainByName((string)$domain)->getOrCall(function () {
            throw new EntityNotFoundException();
        });

        $this->sendConfigurationForDomain($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function findDomainsToBeNotified()
    {
        $uninstalled       = $this->domainRepo->findUninstalled();
        $needsNotification = new ArrayCollection();
        foreach ($uninstalled as $domain) {
            $domainNotifications = $this->domainConfigNotificationRepo->findByDomain($domain, new IdentValue('configuration'));
            if ($domainNotifications->isEmpty()) {
                $needsNotification->add($domain);
            }
        }
        return $needsNotification;
    }

    /**
     * @param Domain $domain
     */
    public function sendConfigurationForDomain(Domain $domain)
    {
        $notification = new DomainNotification();
        $notification->setType(new IdentValue('configuration'));
        $notification->setDomain($domain);
        $this->domainConfigNotificationRepo->persist($notification)->flush();

        $hivDomain = HivDomainValue::create($domain->getName());
        $data      = array(
            'firstname'             => $domain->getOwner()->getFirstname(),
            'surname'               => $domain->getOwner()->getSurname(),
            'domainName'            => $hivDomain->toUTF8(),
            'secondLevelDomainName' => $hivDomain->getSecondLevel(),
            'forward'               => $domain->getActiveBanner()->getRedirectUrl()
        );

        $this->mailer->sendContentTemplateMail(
            'domain.configuration',
            $domain->getActiveBanner()->getLanguage(),
            $domain->getOwnerEmail(),
            $domain->getOwnerName(),
            $data
        );
    }

}
