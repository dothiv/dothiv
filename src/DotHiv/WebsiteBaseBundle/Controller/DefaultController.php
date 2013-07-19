<?php

namespace DotHiv\WebsiteBaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DotHivWebsiteBaseBundle:Default:index.html.twig', array('name' => $name));
    }
}
