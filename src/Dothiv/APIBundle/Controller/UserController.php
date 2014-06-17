<?php

namespace Dothiv\APIBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Form\UserRegisterType;
use Dothiv\BusinessBundle\Form\UserEditType;
use Dothiv\BusinessBundle\Form\ProjectType;
use Dothiv\BusinessBundle\Entity\Project;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
     *   output="Dothiv\BusinessBundle\Entity\User"
     * )
     *
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function postUsersAction() {
        $user = new User();
        $user->setEnabled(true); // TODO: we need to make sure the new user is human (and not registered by bots)

        // generate a random username
        $randomUsername = $user->newRandomCode();
        $user->setUsername($randomUsername);

        $form = $this->createForm(new UserRegisterType(), $user);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $this->container->get('fos_user.user_manager')->updateUser($user, false);
            $em->flush();

            // TODO: allow dashes/email adresses in slug
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
     *     200="Successful",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Entity\User"
     * )
     *
     * @Secure(roles="ROLE_USER")
     */
    public function getUserAction($slug) {
        $context = $this->get('security.context');
        if ($context->isGranted('ROLE_ADMIN') || $context->getToken()->getUsername() == $slug) {
            $user = $this->getDoctrine()->getManager()->getRepository('DothivBusinessBundle:User')->findOneBy(array('username' => $slug));
            return $this->createForm(new UserEditType(), $user);
        }
        throw new HttpException(403);
    }

    /**
     * Updates the requested user.
     *
     * @ApiDoc(
     *   section="user",
     *   resource=true,
     *   description="Updates the requested user.",
     *   statusCodes={
     *     200="Successful",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Entity\User"
     * )
     *
     * @Secure(roles="ROLE_USER")
     */
    public function putUserAction($slug) {
        $context = $this->get('security.context');
        if ($context->isGranted('ROLE_ADMIN') || $context->getToken()->getUsername() == $slug) {
            // fetch user from database
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('DothivBusinessBundle:User')->findOneBy(array('username' => $slug));

            // apply form
            $form = $this->createForm(new UserEditType(), $user);
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em->persist($user);
                $this->container->get('fos_user.user_manager')->updateUser($user, false);
                $em->flush();

                return null;
            }

            return array('form' => $form);
        }
        throw new HttpException(403);
    }

    /**
     * Gets this user's domains.
     *
     * @ApiDoc(
     *   section="user",
     *   resource=true,
     *   description="Gets a list of this user's domains",
     *   statusCodes={
     *     200="Successful",
     *     403="Access denied"
     *   },
     *   output="Dothiv\BusinessBundle\Entity\Domain"
     * )
     *
     * @Secure(roles="ROLE_USER")
     */
    public function getUserDomainsAction($slug) {
        $context = $this->get('security.context');
        if (!$context->isGranted('ROLE_ADMIN') && $context->getToken()->getUsername() !== $slug)
            throw new HttpException(403);

        // retrieve user object from database
        $user = $this->getDoctrine()->getManager()->getRepository('DothivBusinessBundle:User')->findOneBy(array('username' => $slug));
        return $user->getDomains();
    }
}
