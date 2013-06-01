<?php

namespace DotHiv\APIBundle\Controller;

use FOS\Rest\Util\Codes;

use DotHiv\BusinessBundle\Form\ProjectType;
use DotHiv\BusinessBundle\Entity\Project;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class LoginController extends FOSRestController
{
    /**
     * Log in.
     * 
     * @ApiDoc(
     *   resource=true,
     *   description="Log in.",
     *   statusCodes={
     *     201="Successfully created",
     *   },
     *   output="<empty>"
     * )
     */
    public function postLoginsAction() {
        return;
    }
}
