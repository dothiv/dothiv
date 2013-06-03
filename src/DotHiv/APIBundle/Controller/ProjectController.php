<?php

namespace DotHiv\APIBundle\Controller;

use FOS\Rest\Util\Codes;

use DotHiv\BusinessBundle\Form\ProjectType;
use DotHiv\BusinessBundle\Entity\Project;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ProjectController extends FOSRestController
{

    /**
     * Gets the list of all projects.
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *   section="project",
     *   resource=true,
     *   description="Returns all projects.",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */ 
    public function getProjectsAction() {
        $em = $this->getDoctrine()->getManager();
        $list = $em->getRepository('DotHivBusinessBundle:Project')->findAll();
        foreach($list as $obj) {
            $obj->setTranslatableLocale($this->getRequest()->getPreferredLanguage());
            $em->refresh($obj);
        }
        return $list;
    }
    
    /**
     * Gets one specific project.
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *   section="project",
     *   resource=true,
     *   description="Returns a project.",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="DotHiv\BusinessBundle\Form\ProjectType"
     * )
     */
    public function getProjectAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('DotHivBusinessBundle:Project')->find($slug);
        $obj->setTranslatableLocale($this->getRequest()->getPreferredLanguage());
        $em->refresh($obj);
        return $obj;
    }    
    
    /**
     * Puts changes to one specific project.
     * 
     * @Rest\View()
     * 
     * @ApiDoc(
     *   section="project",
     *   resource=true,
     *   description="Changes a specific project",
     *   statusCodes={
     *     200="Returned when successful"
     *   }
     * )
     */
    public function putProjectAction($slug) {
        return $this->processProjectForm($this->loadProject($slug), false);
    }
    
    /**
     * Posts a new project.
     * 
     * @ApiDoc(
     *   section="project",
     *   resource=true,
     *   description="Creates a new project",
     *   statusCodes={
     *     201="Successfully created",
     *   },
     *   output="DotHiv\BusinessBundle\Form\ProjectType"
     * )
     */
    public function postProjectsAction() {
        return $this->processProjectForm(new Project(), true);
    }
    
    // private helper functions
    
    /**
     * Return project with given ID.
     * 
     * @param int $slug
     */
    private function loadProject($slug) {       
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('DotHivBusinessBundle:Project')
                     ->find($slug);
        
        if (!$project) {
            throw $this->createNotFoundException("No such project.");
        }
        
        return $project;
    }
    
    /**
     * Read changes on project from request and apply them to given project instance.
     * 
     * @param Project $project The project instance to updated
     * @param unknown_type $isNew Whether the given project instance is new
     */
    private function processProjectForm(Project $project, $isNew) {
        $form = $this->createForm(new ProjectType(), $project);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($isNew) {
                $em->persist($project);
            }
            $project->setTranslatableLocale($this->getRequest()->getPreferredLanguage());
            $em->flush();
            if ($isNew) {
                $resp = $this->redirectView($this->generateUrl('get_project',
                                array('slug' => $project->getId())), Codes::HTTP_CREATED);
                $resp->setData($project);
                return $resp;
            } else {
                return null;
            }
        }
        return array('form' => $form);
    }
}
