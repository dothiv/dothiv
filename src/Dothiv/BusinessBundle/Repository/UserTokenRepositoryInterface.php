<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use PhpOption\Option;

/**
 * This repository contains the users tokens.
 */
interface UserTokenRepositoryInterface extends ObjectRepository
{
    /**
     * @param User      $user
     * @param \DateTime $minLifetime
     *
     * @return UserToken[]|ArrayCollection
     */
    public function getActiveTokens(User $user, \DateTime $minLifetime);

    /**
     * @param string $bearerToken
     *
     * @return Option
     */
    public function getTokenByBearerToken($bearerToken);

    /**
     * Persist the user token.
     *
     * @param UserToken $userToken
     *
     * @return self
     */
    public function persist(UserToken $userToken);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
