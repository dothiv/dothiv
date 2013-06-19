<?php

namespace DotHiv\BusinessBundle\Tests\Entity;

use DotHiv\BusinessBundle\Entity\User;
use DotHiv\BusinessBundle\Entity\Domain;

class DomainTest extends \PHPUnit_Framework_TestCase {

    public function testGetAndHasAndSetOwner() {
        // create a new domain
        $domain = new Domain();

        // test behavior with empty owner
        $owner = $domain->getOwner();
        $this->assertEquals(null, $owner);
        $this->assertEquals(false, $domain->hasOwner());

        // test owner setting behavior
        $owner = new User();
        $domain->setOwner($owner);
        $this->assertEquals($owner, $domain->getOwner());
        $this->assertEquals(true, $domain->hasOwner());

        // test owner transfering behavior
        $newOwner = new User();
        $domain->setOwner($newOwner);
        $this->assertEquals($newOwner, $domain->getOwner());

        // test owner removing behavior
        $domain->setOwner(null);
        $this->assertEquals(null, $domain->getOwner());
        $this->assertEquals(false, $domain->hasOwner());

        // test setting another owner behavior
        $domain->setOwner($owner);
        $this->assertEquals($owner, $domain->getOwner());
        $this->assertEquals(true, $domain->hasOwner());
    }

    public function testClaim() {
        // create a new domain and a new user
        $domain = new Domain();
        $user = new User();

        // try to claim with empty token
        $this->setExpectedException('InvalidArgumentException', 'Given token is empty');
        $domain->claim($user, '');
        $this->assertEquals(false, $domain->hasOwner());

        // set token
        $domain->setClaimingToken('s3cret');

        // try to claim with wrong token
        $this->setExpectedException('InvalidArgumentException', 'Given token did not match');
        $domain->claim($owner, 'wr0ng');
        $this->assertEquals(false, $domain->hasOwner());

        // try to claim with correct token
        $domain->claim($owner, 's3cret');
        $this->assertEquals($owner, $domain->getOwner());
        $this->assertEquals(false, $domain->getClaimingToken());

        // try to claim again with correct token
        $evilUser = new User();
        $this->setExpectedException('InvalidArgumentException', 'Given token did not match');
        $domain->claim($evilUser, 's3cret');
        $this->assertEquals($owner, $domain->getOwner());

        // try to claim again with empty token
        $this->setExpectedException('InvalidArgumentException', 'Given token is empty');
        $domain->claim($evilUser, '');
        $this->assertEquals($owner, $domain->getOwner());
    }

    public function testDomainUsersUpdate() {
        // create new domains and new users
        $domain1 = new Domain();
        $domain2 = new Domain();
        $user1 = new User();
        $user2 = new User();

        // the new user should not own any domains right now
        $this->assertEquals(0, $user1->getDomains()->count());

        // set user as owner of the domain
        $domain1->setOwner($user1);
        $this->assertEquals($domain1, $user1->getDomains()->first());
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals($user1, $domain1->getOwner());

        // add another domain
        $domain2->setOwner($user1);
        $this->assertEquals($domain2, $user1->getDomains()->last());
        $this->assertEquals(2, $user1->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());

        // transfer a domain to another user
        $domain1->setOwner($user2);
        $this->assertEquals($domain1, $user2->getDomains()->first());
        $this->assertEquals($domain2, $user1->getDomains()->first());
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals(1, $user2->getDomains()->count());
        $this->assertEquals($user1, $domain2->getOwner());
        $this->assertEquals($user2, $domain1->getOwner());

        // remove domain from user
        $domain1->setOwner(null);
        $this->assertEquals(1, $user1->getDomains()->count());
        $this->assertEquals(0, $user2->getDomains()->count());
    }
}