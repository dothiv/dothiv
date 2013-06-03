<?php

namespace DotHiv\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DotHiv\BusinessBundle\Entity\User;
use DotHiv\BusinessBundle\Form\UserRegisterType;
use DotHiv\BusinessBundle\Form\ProjectType;
use DotHiv\BusinessBundle\Entity\Project;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends FOSRestController {

    /**
     * Creates a new user.
     * 
     * @ApiDoc(
     *   section="user",
     *   resource=true,
     *   description="Creates a new user",
     *   statusCodes={
     *     201="Successfully created",
     *     400="Username or Email address already in use"
     *   },
     *   output="DotHiv\BusinessBundle\Entity\User"
     * )
     */
    public function postUsersAction() {
        $user = new User();
        $form = $this->createForm(new UserRegisterType(), $user);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $this->container->get('fos_user.user_manager')->updateUser($user, false);
            $em->flush();

            $response = $this->redirectView($this->generateUrl('get_user', array('slug' => $user->getUsername())), Codes::HTTP_CREATED);
            $response->setData($user);
            return $response;
        }

        return array('form' => $form);
    }
    
    /**
     * Returns the requested user.
     * 
     * @ApiDoc(
     *   section="user",
     *   resource=true,
     *   description="Gets information about the requested user",
     *   statusCodes={
     *     200="Successfully created",
     *     403="Access denied"
     *   },
     *   output="DotHiv\BusinessBundle\Entity\User"
     * )
     */
    public function getUserAction($slug) {
        $context = $this->get('security.context');
        if ($context->isGranted('ROLE_ADMIN') || $context->getToken()->getUsername() == $slug)
            return $this->getDoctrine()->getEntityManager()->getRepository('DotHivBusinessBundle:User')->findBy(array('username' => $slug));
        throw new HttpException(403);
    }

}
