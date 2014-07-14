<?php

namespace Dothiv\BusinessBundle\Exception;

use Dothiv\BusinessBundle\Exception;

class EntityNotFoundException extends \Doctrine\ORM\EntityNotFoundException implements Exception
{
}
