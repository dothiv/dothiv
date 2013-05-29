<?php

namespace DotHiv\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartialController extends Controller
{
    public function view1Action()
    {
        return $this->render('DotHivWebsiteBundle:Partials:partial1.html.twig');
    }
}
