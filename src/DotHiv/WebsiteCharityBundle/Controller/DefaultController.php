<?php

namespace DotHiv\WebsiteCharityBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // get request
        $request = $this->getRequest();

	$url1 = $request->getUri();
	$url2 = preg_replace('/\?_escaped_fragment_=/', '#!', $url1);

	if($url1 === $url2){
		// we go ahead and return the regular index site with our angular app
		return $this->render('DotHivWebsiteCharityBundle:Default:index.html.twig');
	} else {
		// we need to return a pre-rendered version of the requested site
		// TODO: return dom from db
		return new Response("gbot_switch test. url: " . $url1 . " Locale:  " . $request->getPreferredLanguage());
	}
    }
}
