<?php

namespace Dothiv\APIBundle\Controller\Traits;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use PhpOption\Option;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

trait DomainNameTrait
{
    /**
     * Gets a domain by its name and chekcs its permissions.
     *
     * @param string                    $domainname
     * @param SecurityContextInterface  $securityContext
     * @param DomainRepositoryInterface $domainRepo
     *
     * @return Domain
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function getDomainByName($domainname, SecurityContextInterface $securityContext, DomainRepositoryInterface $domainRepo)
    {
        /* @var Domain $domain */
        $domain = $domainRepo->getDomainByName($domainname)->getOrCall(function () use ($domainname) {
            throw new NotFoundHttpException(
                sprintf(
                    'Unknown domain: "%s"!',
                    $domainname
                )
            );
        });

        if (Option::fromValue($domain->getOwner())->isEmpty()) {
            throw new AccessDeniedHttpException(sprintf(
                'Domain "%s" has not been claimed.', $domainname
            ));
        }

        $user = Option::fromValue($securityContext->getToken()->getUser())->getOrCall(function () {
            throw new AccessDeniedHttpException();
        });

        if ($domain->getOwner()->getHandle() !== $user->getHandle()) {
            throw new AccessDeniedHttpException();
        }
        return $domain;
    }
} 
