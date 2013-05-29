<?php

namespace DotHiv\WebsiteCharityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartialController extends Controller
{
    public function view1Action()
    {
        return $this->render('DotHivWebsiteCharityBundle:Partials:partial1.html.twig');
    }
}
