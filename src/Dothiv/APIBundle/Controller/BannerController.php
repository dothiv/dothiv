<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Entity\Banner;
use JMS\Serializer\Serializer;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\ValidatorInterface;

class BannerController
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        BannerRepositoryInterface $bannerRepo,
        ValidatorInterface $validator,
        Serializer $serializer
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->bannerRepo      = $bannerRepo;
        $this->securityContext = $securityContext;
        $this->validator       = $validator;
        $this->serializer      = $serializer;
    }

    /**
     * Returns a specific banner.
     *
     * @ApiDoc(
     *   section="banner",
     *   resource=true,
     *   description="Returns a banner",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="Dothiv\BusinessBundle\Form\BannerType"
     * )
     */
    public function getBannerAction($id)
    {
        // TODO: security concern: who is allowed to GET banner information?

        // retrieve banner from database
        $banner = $this->getDoctrine()->getManager()->getRepository('DothivBusinessBundle:Banner')->findOneBy(array('id' => $id));
        return $this->createForm(new BannerType(), $banner);
    }

    /**
     * Creates a new banner.
     *
     * @ApiDoc(
     *   section="banner",
     *   resource=true,
     *   description="Creates a new banner",
     *   statusCodes={
     *     201="Successfully created"
     *   },
     *   output="Dothiv\BusinessBundle\Form\BannerType"
     * )
     */
    public function postBannersAction()
    {
        // TODO: security concern: who is allowed to create new banners?
        $banner = new Banner();

        $form = $this->createForm(new BannerType(), $banner);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            // push configuration
            if ($banner->getDomain())
                $this->get('clickcounter')->setup($banner->getDomain(), $banner);

            // persist the new banner
            $em = $this->getDoctrine()->getManager();
            $em->persist($banner);
            $em->flush();

            // prepare response
            $response = $this->redirectView($this->generateUrl('get_banner', array('id' => $banner->getId())), Codes::HTTP_CREATED);
            $response->setData($this->createForm(new BannerType(), $banner));
            return $response;
        }

        return array('form' => $form);
    }

    /**
     * Updates the banner for the given domain.
     */
    public function setConfigAction(Request $request, $name)
    {
        $domain = $this->getDomainByName($name);

        /* @var Banner $banner */
        $banner = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($domain) {
            $banner = new Banner();
            $banner->setDomain($domain);
            $domain->setActiveBanner($banner);
            return $banner;
        });

        $banner->setLanguage($request->get('language'));
        $banner->setPosition($request->get('position_first'));
        $banner->setPositionAlternative($request->get('position'));

        $errors = $this->validator->validate($banner);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        $this->bannerRepo->persist($banner)->flush();
        $this->domainRepo->persist($domain)->flush();

        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * Gets the banner config for the given domain.
     */
    public function getConfigAction(Request $request, $name)
    {
        $domain = $this->getDomainByName($name);
        $banner = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($name) {
            throw new NotFoundHttpException(
                sprintf(
                    'No banner configured for domain "%s"!',
                    $name
                )
            );
        });

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
        $response->setContent($this->serializer->serialize($banner, 'json'));
        return $response;
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

    /**
     * @param $name
     *
     * @return Domain
     * @throws NotFoundHttpException If no domain is found.
     * @throws AccessDeniedHttpException If user is not the owner of the domain.
     */
    protected function getDomainByName($name)
    {
        /* @var Domain $domain */
        $domain = $this->domainRepo->getDomainByName($name)->getOrCall(function () use ($name) {
            throw new NotFoundHttpException(
                sprintf(
                    'Unknown domain: "%s"!',
                    $name
                )
            );
        });

        if ($domain->getOwner()->getHandle() !== $this->securityContext->getToken()->getUser()->getHandle()) {
            throw new AccessDeniedHttpException();
        }
        return $domain;
    }
}
