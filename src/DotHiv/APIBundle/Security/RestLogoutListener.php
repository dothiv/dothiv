<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\LogoutListener;

/**
 * Only handles this request as a logout if the HTTP 'DELETE' method is used.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class RestLogoutListener extends LogoutListener {

    /**
     * @param Request $request
     */
    protected function requiresLogout(Request $request) {
        return $request->getMethod() == 'DELETE' && parent::requiresLogout($request);
    }

}