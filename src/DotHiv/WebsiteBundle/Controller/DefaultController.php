<?php

namespace DotHiv\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DotHivWebsiteBundle:Default:index.html.twig');
    }
}
