<?php

namespace DotHiv\APIBundle\Controller;

use FOS\RestBundle\View\View;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DotHiv\BusinessBundle\Entity\DomainClaim;
use DotHiv\BusinessBundle\Form\DomainClaimType;
use FOS\Rest\Util\Codes;
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
     *   output="DotHiv\BusinessBundle\Form\DomainClaimType"
     * )
     */
    public function postDomainClaimsAction() {
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
            $domain = $em->getRepository('DotHivBusinessBundle:Domain')->findOneBy(array('claimingToken' => $token));

            // check if token exists and is connected to a domain
            if ($domain === null || $token == '')
                throw new HttpException(400);

            // retrieve the user object
            $user = $em->getRepository('DotHivBusinessBundle:User')->findOneBy(array('username' => $username));

            // set the given user as owner of the domain and void token
            $domain->setOwner($user);
            $domain->setClaimingToken(null);

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