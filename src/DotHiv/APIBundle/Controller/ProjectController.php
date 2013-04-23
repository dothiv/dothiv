<?php

namespace DotHiv\APIBundle\Controller;
use DotHiv\BusinessBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ProjectController extends Controller
{

    /**
     * Gets the list of all projects.
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Returns all projects.",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */ 
    public function getProjectsAction()
    {
        return $this->getDoctrine()->getManager()->getRepository('DotHivBusinessBundle:Project')->findAll();
    }
    
    /**
     * Gets one specific flight.
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Returns a flight.",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="NilsWisiol\FlightManagementBundle\Form\FlightType"
     * )
     */
    public function getProjectAction($slug) {
    	return $this->getDoctrine()->getManager()->getRepository('DotHivBusinessBundle:Project')->find($slug);
    }    
}
