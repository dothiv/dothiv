<?php

namespace Dothiv\BusinessBundle\Service;

/**
 * This exception will be thrown if there's a problem with the
 * click counter API.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@click4life.hiv>
 */
class ClickCounterException extends \Exception
{
    /**
     * @var string
     */
    public $response;
}
