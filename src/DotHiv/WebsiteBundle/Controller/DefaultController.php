<?php

namespace DotHiv\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // get request
        $request = $this->getRequest();

        // check for the '_escaped_fragment_' parameter
        if($request->query->has('_escaped_fragment_') === false) {
            // we go ahead and return the regular index site with our angular app
            return $this->render('DotHivWebsiteBundle:Default:index.html.twig');
        } else {
            // we need to return a pre-rendered version of the requested site
            $fragment = $request->query->get('_escaped_fragment_');
            $language = $request->getPreferredLanguage();

            // TODO: check for the requested path in table of pre-rendered sites
            // TODO: return pre-rendered html instead of dummy output
            return new Response("This site should show a pre-rendered version of: '" . $fragment ."' in language $language.");
        }
    }
}
