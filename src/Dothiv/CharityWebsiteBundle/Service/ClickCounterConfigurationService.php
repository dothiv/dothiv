<?php


namespace Dothiv\CharityWebsiteBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Exception\EntityNotFoundException;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class ClickCounterConfigurationService implements SendClickCounterConfigurationServiceInterface
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var UserReminderRepositoryInterface
     */
    private $userReminderRepo;

    /**
     * @var ContentMailerInterface
     */
    private $mailer;

    public function __construct(
        DomainRepositoryInterface $domainRepo,
        UserReminderRepositoryInterface $userNotificationRepo,
        ContentMailerInterface $mailer
    )
    {
        $this->domainRepo       = $domainRepo;
        $this->userReminderRepo = $userNotificationRepo;
        $this->mailer           = $mailer;
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
            $userNotification = $this->userReminderRepo->findByTypeAndItem(new IdentValue('configuration'), $domain);
            if ($userNotification->isEmpty()) {
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
        $notification = new UserReminder();
        $notification->setType(new IdentValue('configuration'));
        $notification->setIdent($domain);
        $this->userReminderRepo->persist($notification)->flush();

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
