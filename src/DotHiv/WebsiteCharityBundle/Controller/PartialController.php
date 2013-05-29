<?php

namespace DotHiv\WebsiteCharityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartialController extends Controller
{
    public function homeAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:home.html.twig');
    }

    public function loginAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:login.html.twig');
    }
}
