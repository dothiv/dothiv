<?php

namespace DotHiv\BusinessBundle\Tests\Entity;

use DotHiv\BusinessBundle\Entity\User;
use DotHiv\BusinessBundle\Entity\Domain;

class UserTest extends \PHPUnit_Framework_TestCase {
    /**
     * Tests the wiring between Domain and User class.
     * See also Tests\DomainTest
     */
    public function testUserDomainsUpdate() {
        // create new domains and new users
        $domain1 = new Domain();
        $domain2 = new Domain();
        $user1 = new User();
        $user2 = new User();

        // the new user should not own any domains right now
        $this->assertEquals(0, $user1->getDomains()->count());

        // set user as owner of the domain
        $user1->addDomain($domain1);
        $this->assertEquals($domain1, $user1->getDomains()->first());
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals($user1, $domain1->getOwner());

        // add another domain
        $user1->addDomain($domain2);
        $this->assertEquals($domain2, $user1->getDomains()->last());
        $this->assertEquals(2, $user1->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());

        // transfer a domain to another user
        $user2->addDomain($domain1);
        $this->assertEquals($domain1, $user2->getDomains()->first());
        $this->assertEquals($domain2, $user1->getDomains()->first());
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals(1, $user2->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());
        $this->assertEquals($user2, $domain1->getOwner());

        // remove domain from user
        $user2->removeDomain($domain1);
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals(0, $user2->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());
        $this->assertEquals(null, $domain1->getOwner());

        // remove domain again from user
        $user2->removeDomain($domain1);
        $this->assertEquals(0, $user2->getDomains()->count());
        $this->assertEquals(null, $domain1->getOwner());

        // remove domain from user that was not owned by him/her
        $user1->removeDomain($domain1);
        $this->assertEquals(0, $user2->getDomains()->count());
        $this->assertEquals(null, $domain1->getOwner());
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());
    }
}