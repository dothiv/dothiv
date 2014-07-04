<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainClaim;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainClaimRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dothiv\BusinessBundle\Form\DomainType;
use Symfony\Component\Security\Core\SecurityContext;

class DomainController
{
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

    public function __construct(

        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        DomainClaimRepositoryInterface $domainClaimRepo
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->domainClaimRepo = $domainClaimRepo;
        $this->securityContext = $securityContext;
    }

    /**
     * Claims a domain for a user who provides the correct token.
     */
    public function claimAction(Request $request)
    {
        /* @var User $user */
        $user  = $this->securityContext->getToken()->getUser();
        $name  = $request->get('domain');
        $token = $request->get('token');

        /* @var Domain $domain */
        $domain = $this->domainRepo->getDomainByName($name)->getOrCall(function () use ($name) {
            throw new BadRequestHttpException(
                sprintf(
                    'Invalid domain "%s"!',
                    $name
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
        $domain->claim($user, $token);
        $claim = new DomainClaim();
        // persist the successful claim
        $claim->setUsername($user->getUsername());
        $claim->setClaimingToken($token);
        $claim->setDomainname($domain->getName());
        $this->domainClaimRepo->persist($claim)->flush();
        $this->domainRepo->persist($domain)->flush();

        $response = new Response();
        $response->setStatusCode(201);
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
        $em       = $this->getDoctrine()->getManager();
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
}
