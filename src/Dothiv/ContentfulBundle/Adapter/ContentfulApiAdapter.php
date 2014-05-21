<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Item\ContentfulItem;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ContentfulApiAdapter extends LoggerAwareInterface
{
    function sync();
}
