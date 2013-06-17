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
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:profile.html.twig');
    }

    public function profileSummaryAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:summary.html.twig');
    }

    public function profileEditAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:edit.html.twig');
    }

    public function profileProjectsAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:projects.html.twig');
    }

    public function profileDomainsAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:domains.html.twig');
    }

    public function profileVotesAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:votes.html.twig');
    }

    public function profileCommentsAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:comments.html.twig');
    }

    public function registrationAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:registration.html.twig');
    }
}
