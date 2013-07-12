<?php

namespace DotHiv\BusinessBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * DuplicateEmailAddressException is thrown if a registration request
 * is made with an email address that is already used by another user
 * account.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class DuplicateEmailAddressException extends AuthenticationException
{
}
