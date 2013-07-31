<?php

namespace DotHiv\WebsiteCharityBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // get request
	$request = $this->getRequest();
	if($request->query->has('_escaped_fragment_') === false){
		// we go ahead and return the regular index site with our angular app
		return $this->render('DotHivWebsiteCharityBundle:Default:index.html.twig');
	} else {
		// we need to return a pre-rendered version of the requested site
		$domain = $request->getHttpHost();
		$fragment = $request->get('_escaped_fragment_');

		$em = $this->getDoctrine()->getManager();
		$pages = $em->getRepository('DotHivBusinessBundle:Crawler\Page')->findBy(array('host'=>$domain, 'fragment'=>$fragment));
		if(sizeof($pages) == 0){
			throw new HttpException(404, 'did not find page for domain ' . $domain . ' and fragment ' . $fragment . ' in db');
		}
		return new Response(stream_get_contents($pages[0]->getDom()));
	}
    }
}
