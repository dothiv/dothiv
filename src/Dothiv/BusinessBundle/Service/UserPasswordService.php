<?php


namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserPasswordService implements UserPasswordServiceInterface
{
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(User $user, $newPassword)
    {
        $encoder     = $this->encoderFactory->getEncoder($user);
        $encodedPass = $encoder->encodePassword($newPassword, $user->getSalt());
        $user->setPassword($encodedPass);
    }
}
