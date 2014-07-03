<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Form\UserEditType;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\SecurityContext;

class UserController
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    public function __construct(

        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        Serializer $serializer
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->securityContext = $securityContext;
        $this->serializer      = $serializer;
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
    public function getUserAction($slug)
    {
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
    public function putUserAction($slug)
    {
        $context = $this->get('security.context');
        if ($context->isGranted('ROLE_ADMIN') || $context->getToken()->getUsername() == $slug) {
            // fetch user from database
            $em   = $this->getDoctrine()->getManager();
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
     */
    public function domainsAction()
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
        $response->setContent($this->serializer->serialize($user->getDomains(), 'json'));
        return $response;
    }
}
