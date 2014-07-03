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
     * Returns one specific domain.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Returns a domain",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="Dothiv\BusinessBundle\Form\DomainType"
     * )
     */
    public function getDomainAction($slug)
    {
        // TODO: security concern: who is allowed to GET domain information?

        // retrieve domain from database
        $domain = $this->getDoctrine()->getManager()->getRepository('DothivBusinessBundle:Domain')->findOneBy(array('id' => $slug));
        return $this->createForm(new DomainType(), $domain);
    }

    /**
     * Returns a list of all domains.
     *
     * @QueryParam(name="token", nullable=true, description="Claiming token")
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Returns a list of all domains",
     *   filters={{"name"="token", "dataType"="string"}},
     *   statusCodes={
     *     200="Returned when successful",
     *     400="Token unknown"
     *   }
     * )
     */
    public function getDomainsAction(ParamFetcher $paramFetcher)
    {
        // TODO: security concern: who is allowed to GET domain information?

        // get query parameter and entity manager
        $token = $paramFetcher->get('token');
        $em = $this->getDoctrine()->getManager();

        if ($token === null) {
            // retrieve list of domains from database
            $list = $em->getRepository('DothivBusinessBundle:Domain')->findAll();
            return $list;
        } else {
            // retrieve requested domain from database
            $domain = $em->getRepository('DothivBusinessBundle:Domain')->findOneBy(array('claimingToken' => $token));
            if ($domain === null)
                throw new HttpException(Codes::HTTP_BAD_REQUEST, 'Invalid token.'); // TODO: better error handling!
            return $this->createForm(new DomainType(), $domain);
        }
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
            $oldForward = $domain->getDnsForward();
            $form = $this->createForm(new DomainType(), $domain);
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em->persist($domain);
                $em->flush();

                // check if we need to update the DNS
                if ($oldForward != $domain->getDnsForward()) {
                    // DNS forward configuration changed
                    if ($domain->getDnsForward())
                        $this->get('dns')->forward($domain);
                    else
                        $this->get('dns')->reset($domain);
                }

                return null;
            }

            return array('form' => $form);
        }
        throw new HttpException(403);
    }

    /**
     * Gets the banners of this domain.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Gets a list of banners of this domain",
     *   statusCodes={
     *     200="Successful",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Entity\Banner"
     * )
     */
    public function getDomainBannersAction($id)
    {
        // TODO: security concern: who is allowed to get domain banners?

        // retrieve domain from database
        $domain = $this->getDoctrine()->getManager()->getRepository('DothivBusinessBundle:Domain')->findOneBy(array('id' => $id));

        // return list of banners
        return $domain->getBanners();
    }

    // ----------- private functions go here -----------

    /**
     * Generates a 32 digit random code
     *
     * Used pool of characters: a-zA-Z0-9
     */
    private function newRandomCode()
    {
        $pool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWWXY0123456789";
        $code = "";
        while (strlen($code) < 32) {
            $code .= substr($pool, rand(0, 61), 1);
        }
        return $code;
    }
}
