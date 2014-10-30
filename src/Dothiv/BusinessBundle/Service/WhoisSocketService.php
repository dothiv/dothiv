<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Exception\RuntimeException;
use Dothiv\ValueObject\HivDomainValue;

/**
 * TODO: Test: http://darrendev.blogspot.jp/2012/07/mock-socket-in-php.html
 */
class WhoisSocketService implements WhoisServiceInterface
{

    private $address = 'whois.afilias-srs.net';

    /**
     * {@inheritdoc}
     */
    public function lookup(HivDomainValue $domain)
    {
        if (($sock = \socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            throw new RuntimeException(socket_strerror(socket_last_error()));
        }

        if (\socket_connect($sock, $this->getAddress(), 43) === false) {
            throw new RuntimeException(socket_strerror(socket_last_error($sock)));
        }

        $data = $domain->toScalar() . "\n";
        \socket_write($sock, $data, strlen($data));

        $response = '';
        if (false === ($bytes = socket_recv($sock, $response, 2048, MSG_WAITALL))) {
            throw new RuntimeException(socket_strerror(socket_last_error($sock)));
        }
        \socket_close($sock);

        return $response;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

} 
