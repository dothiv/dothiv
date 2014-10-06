<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\APIBundle\Request\ClaimRequest;
use Dothiv\APIBundle\Request\DomainNameRequest;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainClaim;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\APIBundle\Annotation\ApiRequest;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainClaimRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class DomainController
{
    use CreateJsonResponseTrait;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var DomainClaimRepositoryInterface
     */
    private $domainClaimRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ClockValue
     */
    private $clock;

    public function __construct(

        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        DomainClaimRepositoryInterface $domainClaimRepo,
        SerializerInterface $serializer,
        EventDispatcherInterface $dispatcher,
        ClockValue $clock
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->domainClaimRepo = $domainClaimRepo;
        $this->securityContext = $securityContext;
        $this->serializer      = $serializer;
        $this->dispatcher      = $dispatcher;
        $this->clock           = $clock;
    }

    /**
     * Claims a domain for a user who provides the correct token.
     *
     * @ApiRequest("Dothiv\APIBundle\Request\ClaimRequest")
     */
    public function claimAction(Request $request)
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();
        /* @var ClaimRequest $model */
        $claimRequest = $request->attributes->get('model');

        $token = $claimRequest->token;

        /* @var Domain $domain */
        $domain = $this->domainRepo->getDomainByToken($token)->getOrCall(function () use ($token) {
            throw new BadRequestHttpException(
                sprintf(
                    'Token "%s" not found!',
                    $token
                )
            );
        });

        if ($domain->getToken() !== $token) {
            throw new BadRequestHttpException(
                sprintf(
                    'Invalid token "%s"!',
                    $token
                )
            );
        }

        // claim the domain
        $this->claimDomain($domain, $user, $token);

        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->setContent($this->serializer->serialize($domain, 'json'));
        return $response;
    }

    /**
     * Claims a domain for a user without a token.
     *
     * @ApiRequest("Dothiv\APIBundle\Request\DomainNameRequest")
     *
     * @throws NotFoundHttpException If domain not found
     * @throws ConflictHttpException if already mailed claiming token.
     * @throws GoneHttpException if already claimed
     */
    public function claimNoTokenAction(Request $request)
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();
        /* @var DomainNameRequest $model */
        $domainRequest = $request->attributes->get('model');

        /* @var Domain $domain */
        $domain = $this->domainRepo->getDomainByName($domainRequest->getName())->getOrCall(function () {
            throw new NotFoundHttpException();
        });

        // Domain is claimed?
        if (Option::fromValue($domain->getOwner())->isDefined()) {
            throw new GoneHttpException('Already claimed.');
        }

        // Not owned by user …
        if ($domain->getOwnerEmail() != $user->getEmail()) {
            if (Option::fromValue($domain->getTokenSent())->isDefined()) {
                throw new ConflictHttpException('Mail already sent.');
            }
            $this->dispatcher->dispatch(BusinessEvents::CLAIM_TOKEN_REQUESTED, new DomainEvent($domain));
            $domain->setTokenSent($this->clock->getNow());
            $this->domainRepo->persist($domain)->flush();
            $response = $this->createResponse();
            $response->setStatusCode(202);
            return $response;
        }

        // User is owner: claim the domain
        $this->claimDomain($domain, $user, $domain->getToken());
        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->setContent($this->serializer->serialize($domain, 'json'));
        return $response;
    }

    /**
     * Updates the domain.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Updates the domain.",
     *   statusCodes={
     *     200="Successful",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Entity\Domain"
     * )
     *
     * @Secure(roles="ROLE_USER")
     */
    public function putDomainAction($slug)
    {
        $context = $this->get('security.context');

        // fetch domain from database
        $em     = $this->getDoctrine()->getManager();
        $domain = $em->getRepository('DothivBusinessBundle:Domain')->findOneBy(array('id' => $slug));

        if ($context->isGranted('ROLE_ADMIN') || $context->getToken()->getUsername() == $domain->getOwner()->getUsername()) {

            // apply form
            $form = $this->createForm(new DomainType(), $domain);
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em->persist($domain);
                $em->flush();

                return null;
            }

            return array('form' => $form);
        }
        throw new HttpException(403);
    }

    /**
     * @param Domain $domain
     * @param User   $user
     * @param string $token
     */
    protected function claimDomain(Domain $domain, User $user, $token)
    {
        $domain->claim($user, $token);
        $claim = new DomainClaim();
        // persist the successful claim
        $claim->setUsername($user->getUsername());
        $claim->setClaimingToken($token);
        $claim->setDomainname($domain->getName());
        $this->domainClaimRepo->persist($claim)->flush();
        $this->domainRepo->persist($domain)->flush();
    }
}
