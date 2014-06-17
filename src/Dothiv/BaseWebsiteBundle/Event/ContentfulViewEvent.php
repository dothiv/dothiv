<?php

namespace Dothiv\BaseWebsiteBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ContentfulViewEvent extends Event
{
    /**
     * @var \stdClass
     */
    private $view;

    public function __construct(\stdClass $view)
    {
        $this->view = $view;
    }

    /**
     * @return \stdClass
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \stdClass $view
     */
    public function setView(\stdClass $view)
    {
        $this->view = $view;
    }
}
