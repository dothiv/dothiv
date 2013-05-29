<?php

namespace DotHiv\WebsiteCompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DotHivWebsiteCompanyBundle:Default:index.html.twig', array('name' => $name));
    }
}
