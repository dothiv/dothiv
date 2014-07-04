<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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

    /**
     * @var Serializer
     */
    private $serializer;

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
     *
     * @throws AccessDeniedHttpException If tries to fetch domains for another user.
     */
    public function domainsAction($handle)
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();
        if ($user->getHandle() !== $handle) {
            throw new AccessDeniedHttpException();
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
        $response->setContent($this->serializer->serialize($user->getDomains(), 'json'));
        return $response;
    }
}
