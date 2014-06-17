<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Psr\Log\LoggerAwareInterface;

interface ContentfulApiAdapter extends LoggerAwareInterface
{
    function sync();
}
