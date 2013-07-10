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

    public function aboutAction() 
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/About:about.html.twig');
    }

    public function aboutIdeaAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/About:idea.html.twig');
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

    public function profileDomainEditAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:domain-edit.html.twig');
    }

    public function profileDomainClaimAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:domain-claim.html.twig');
    }

    public function profileDomainEditorsAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:domain-editors.html.twig');
    }

    public function profileVotesAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:votes.html.twig');
    }

    public function profileCommentsAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials/Profile:comments.html.twig');
    }

    public function mockAction()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:mock.html.twig');
    }
}
