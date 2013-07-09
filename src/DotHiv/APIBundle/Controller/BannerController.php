<?php

namespace DotHiv\APIBundle\Controller;

use FOS\Rest\Util\Codes;
use DotHiv\BusinessBundle\Entity\Banner;
use DotHiv\BusinessBundle\Form\BannerType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BannerController extends FOSRestController {
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
     *   output="DotHiv\BusinessBundle\Form\BannerType"
     * )
     */
    public function getBannerAction($id) {
        // TODO: security concern: who is allowed to GET banner information?

        // retrieve banner from database
        $banner = $this->getDoctrine()->getManager()->getRepository('DotHivBusinessBundle:Banner')->findOneBy(array('id' => $id));
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
     *   output="DotHiv\BusinessBundle\Form\BannerType"
     * )
     */
    public function postBannersAction() {
        // TODO: security concern: who is allowed to create new banners?
        $banner = new Banner();

        $form = $this->createForm(new BannerType(), $banner);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            // TODO: immediate association with domain?

            // persist the new banner
            $em = $this->getDoctrine()->getManager();
            $em->persist($banner);
            $em->flush();

            // prepare response
            $response = $this->redirectView($this->generateUrl('get_banner', array('slug' => $banner->getId())), Codes::HTTP_CREATED);
            $response->setData($this->createForm(new BannerType(), $banner));
            return $response;
        }

        return array('form' => $form);
    }

    /**
     * Updates the banner.
     *
     * @ApiDoc(
     *   section="banner",
     *   resource=true,
     *   description="Updates the banner.",
     *   statusCodes={
     *     200="Successful"
     *   }
     * )
     */
    public function putBannerAction($id) {
            // fetch banner from database
            $em = $this->getDoctrine()->getManager();
            $banner = $em->getRepository('DotHivBusinessBundle:Banner')->findOneBy(array('id' => $id));

            // apply form
            $form = $this->createForm(new BannerType(), $banner);
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                // persist the updated banner
                $em->persist($banner);
                $em->flush();
                return null;
            }

            return array('form' => $form);
    }
}