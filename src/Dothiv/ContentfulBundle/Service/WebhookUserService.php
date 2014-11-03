<?php


namespace Dothiv\ContentfulBundle\Service;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WebhookUserService implements UserProviderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if ($username != $this->config['httpBasicUsername']) {
            throw new UsernameNotFoundException();
        }
        return new WebhookUser($this->config['httpBasicUsername'], $this->config['httpBasicPassword']);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Dothiv\ContentfulBundle\Service\WebhookUser';
    }
} 
