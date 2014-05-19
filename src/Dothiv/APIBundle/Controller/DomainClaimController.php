<?php

namespace Dothiv\APIBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dothiv\BusinessBundle\Entity\DomainClaim;
use Dothiv\BusinessBundle\Form\DomainClaimType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DomainClaimController extends FOSRestController {
    /**
     * Claims a domain for a user who provides the correct token.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=false,
     *   description="Claims a domain for a user",
     *   statusCodes={
     *     201="Successfully created",
     *     400="Token unknown",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Form\DomainClaimType"
     * )
     *
     * @Secure(roles="ROLE_USER")
     */
    public function postDomainsClaimsAction() {
        // create a new claim object and the corresponding form
        $claim = new DomainClaim();
        $form = $this->createForm(new DomainClaimType(), $claim);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            // check if user is logged in and claims the domain for himself (or is admin)
            $context = $this->get('security.context');
            $username = $form->get('username')->getData();
            if (!$context->isGranted('ROLE_ADMIN') && $context->getToken()->getUsername() !== $username)
                throw new HttpException(403, "User $username is not allowed to claim this domain.");

            // find domain that matches the token
            $token = $form->get('claimingToken')->getData();
            $em = $this->getDoctrine()->getManager();
            $domain = $em->getRepository('DothivBusinessBundle:Domain')->findOneBy(array('claimingToken' => $token));

            // check if token exists and is connected to a domain
            if ($domain === null || $token == '')
                throw new HttpException(Codes::HTTP_BAD_REQUEST);

            // retrieve the user object
            $user = $em->getRepository('DothivBusinessBundle:User')->findOneBy(array('username' => $username));

            // claim the domain
            $domain->claim($user, $token);

            // persist the successful claim
            $claim->setUsername($username);
            $claim->setClaimingToken($token);
            $claim->setDomain($domain->getName());

            $em->persist($claim);
            $em->flush();

            $view = new View($this->createForm(new DomainClaimType(), $claim), Codes::HTTP_CREATED);
            return $view;
        }

        return array('form' => $form);
    }
}
