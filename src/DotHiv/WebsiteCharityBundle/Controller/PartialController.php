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

    public function profileAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:profile.html.twig');
    }

    public function profileSummaryAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:profile_summary.html.twig');
    }
    
    public function profileEditAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:profile_edit.html.twig');
    }

    public function registrationAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:registration.html.twig');
    }
}
