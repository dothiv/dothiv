<?php

namespace DotHiv\APIBundle\Controller;

use DotHiv\BusinessBundle\Form\DomainType;
use DotHiv\BusinessBundle\Entity\Domain;
use DotHiv\BusinessBundle\Entity\User;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DomainController extends FOSRestController {
    /**
     * Returns one specific domain.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Returns a domain.",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="DotHiv\BusinessBundle\Form\DomainType"
     * )
     */
    public function getDomainAction($slug) {
        // TODO: security concern: who is allowed to GET domain information?

        // retrieve domain from database
        $domain = $this->getDoctrine()->getEntityManager()->getRepository('DotHivBusinessBundle:Domain')->findOneBy(array('id' => $slug));
        return $this->createForm(new DomainType(), $domain);
    }

    /**
     * Creates a new domain.
     *
     * @ApiDoc(
     *   section="domain",
     *   resource=true,
     *   description="Creates a new domain",
     *   statusCodes={
     *     201="Successfully created"
     *   },
     *   output="DotHiv\BusinessBundle\Form\DomainType"
     * )
     */
    public function postDomainsAction() {
        // TODO: security concern: who is allowed to create new domains?
        $domain = new Domain();

        $form = $this->createForm(new DomainType(), $domain);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            // generate a random claiming token
            $claimingToken = $this->newRandomCode();
            $domain->setClaimingToken($claimingToken);

            // persist the new domain
            $em = $this->getDoctrine()->getManager();
            $em->persist($domain);
            $em->flush();

            $response = $this->redirectView($this->generateUrl('get_domain', array('slug' => $domain->getId())), Codes::HTTP_CREATED);
            $response->setData($this->createForm(new DomainType(), $domain));
            return $response;
        }

        return array('form' => $form);
    }

    /**
     * Generates a 32 digit random code
     *
     * Used pool of characters: a-zA-Z0-9
     */
    private function newRandomCode() {
        $pool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWWXY0123456789";
        $code = "";
        while (strlen($code) < 32) {
            $code .= substr($pool, rand(0, 61), 1);
        }
        return $code;
    }
}