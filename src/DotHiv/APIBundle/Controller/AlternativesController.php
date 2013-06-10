<?php

namespace DotHiv\APIBundle\Controller;

use FOS\RestBundle\View\View;

use FOS\RestBundle\View\ViewHandler;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;
use DotHiv\BusinessBundle\Entity\DomainList;
use DotHiv\BusinessBundle\Form\DomainListType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * API endpoint for getting .hiv alternatives.
 * See BusinessBundle\Entity\DomainAlternative.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class AlternativesController extends FOSRestController {

    /**
     * Returns a list of .hiv domain name alternatives for the given set of domains. 
     * 
     * @ApiDoc(
     *   section="alternatives",
     *   resource=true,
     *   description="Find alternatives for a given set of domain names.",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   filters={
     *       {"name"="q", "description"="Query for domain names", "dataType"="comma seperated list of domain names"}
     *   }
     * )
     */
    public function getAlternativesAction() {
        $domainList = explode(',', $this->getRequest()->get('q'));

        if (count($domainList) == 0) 
            throw new HttpException(404);

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('DotHivBusinessBundle:DomainAlternative')->createQueryBuilder('d');
        $result = $qb
            ->where($qb->expr()->in('d.domain', $domainList))
            ->andWhere('d.trusted = true')
            ->getQuery()
            ->getResult();

        $origin = $this->getRequest()->headers->get('Origin');
        $chrome = 'chrome-extension://';
        if (substr($origin, 0, strlen($chrome)) == $chrome) {
            // Allow API calls from Chrome extensions TODO make this a service
            $handler = $this->get('fos_rest.view_handler');
            $view = View::create($result, 200, array(
                    'Access-Control-Allow-Origin' => $origin,
                    'Access-Control-Allow-Headers' => 'X-Requested-With',
                ));
            return $this->handleView($view);
        } else {
            return $result;
        }
    }
    
    public function optionsAlternativesAction() {
        $resp = new Response();

        // Allow API calls from Chrome extensions TODO make this a service
        $origin = $this->getRequest()->headers->get('Origin');
        $chrome = 'chrome-extension://';
        if (substr($origin, 0, strlen($chrome)) == $chrome) {
            $resp->headers->set('Access-Control-Allow-Origin', $origin);
            $resp->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
            $resp->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        }

        return $resp;
    }

}
