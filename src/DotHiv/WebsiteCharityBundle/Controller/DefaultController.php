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

        // check for the '_escaped_fragment_' parameter
        if($request->query->has('_escaped_fragment_') === false) {
            // check which browser is on the other end
            if (preg_match('/MSIE [2345678]/', $this->getRequest()->headers->get('user-agent'))) {
                // send a "sorry, no support" page
                return $this->render('DotHivWebsiteCharityBundle:Default:nosupport.html.twig');
            } else {
                // we go ahead and return the regular index site with our angular app
                return $this->render('DotHivWebsiteCharityBundle:Default:index.html.twig');
            }
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
