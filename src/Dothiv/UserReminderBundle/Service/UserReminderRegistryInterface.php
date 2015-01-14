<?php

namespace Dothiv\UserReminderBundle\Service;

interface UserReminderRegistryInterface
{
    /**
     * Send pending notification
     */
    function send();
}
