<?php

namespace Dothiv\APIBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

/**
 * Sets CORS header appropriately. We don't allow any CORS requests to our
 * API, except from URLs matching the configuration 'allow_origin'.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class CORSListener {

    protected $dispatcher;
    protected $options;

    public function __construct(ContainerAwareEventDispatcher $dispatcher, array $options) {
        $this->dispatcher = $dispatcher;
        $this->options = $options;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->headers->has('Origin')) {
            return;
        }

        $currentPath = $request->getPathInfo() ?: '/';

        if (preg_match('{/api/}i', $currentPath)) {
            if (!$this->checkOrigin($request, $this->options)) {
                $response = new Response('', 403, array('Access-Control-Allow-Origin' => 'null'));
                $event->setResponse($response);
                return;
            }

            $this->dispatcher->addListener('kernel.response', array($this, 'onKernelResponse'));
            return;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
    }

    protected function checkOrigin($request, $options) {
        $origin = $request->headers->get('Origin');
        foreach($options['allow_origin'] as $allowedOrigin) {
            if (preg_match('{'.$allowedOrigin.'}i', $origin)) {
                return true;
            }
        }

        return false;
    }

}
