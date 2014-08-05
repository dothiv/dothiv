<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller is used to render the
 */
class StaticFilesController
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function fileAction($filename)
    {
        $path = sprintf('%s/%s', $this->config['location'], $filename);
        $mime = MimeTypeGuesser::getInstance();
        $mime->guess($path);
        $response = new Response();
        $response->headers->set('content-type', $mime->guess($path));
        $response->setContent(file_get_contents($path));
        return $response;
    }
} 
