<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class UserController
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    public function __construct(
        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        Serializer $serializer
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->securityContext = $securityContext;
        $this->serializer      = $serializer;
    }

    /**
     * Gets this user's domains.
     */
    public function domainsAction()
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
        $response->setContent($this->serializer->serialize($user->getDomains(), 'json'));
        return $response;
    }
}
