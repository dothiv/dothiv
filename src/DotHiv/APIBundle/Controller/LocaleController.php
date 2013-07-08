<?php

namespace DotHiv\APIBundle\Controller;

use DotHiv\APIBundle\Form\LocaleType;
use DotHiv\APIBundle\Entity\Locale;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DotHiv\BusinessBundle\Entity\User;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Used to override the locale auto-detection by setting the
 * user's locale manually.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class LocaleController extends FOSRestController
{

    /**
     * Returns the user's locale.
     *
     * @ApiDoc(
     *   section="locale",
     *   resource=true,
     *   description="Returns the user's locale",
     *   statusCodes={
     *     200="Returned when successful",
     *   },
     *   output="DotHiv\APIBundle\Form\LocaleType"
     * )
     */
    public function getLocaleAction()
    {
        return $this->createForm(new LocaleType(), new Locale($this->getRequest()->getSession()->get('locale', $this->getRequest()->getLocale())));
    }

    /**
     * Edit the user's locale.
     *
     * @ApiDoc(
     *   section="locale",
     *   resource=true,
     *   description="Edit the user's locale. Overrides the sent 'Accept' header.",
     *   statusCodes={
     *     200="Successfully edited"
     *   },
     *   output="DotHiv\APIBundle\Form\LocaleType"
     * )
     */
    public function putLocaleAction()
    {
        $locale = new Locale();

        $form = $this->createForm(new LocaleType(), $locale);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            if ($locale->getLocale() != "") {
                $this->getRequest()->getSession()->set('locale', $locale->getLocale());
            } else {
                $this->getRequest()->getSession()->remove('locale');
            }
            return null;
        }

        return array('form' => $form);
    }
}
