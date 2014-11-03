<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Validator;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Validator\Constraints\ValidDomain;
use Dothiv\BusinessBundle\Validator\Constraints\ValidDomainValidator;

/**
 * @group DotHIVBusiness
 * @group Validation
 * @group ValidDomainValidator
 */
class ValidDomainValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Validator\ExecutionContext
     */
    protected $mockContext;

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            '\Dothiv\BusinessBundle\Validator\Constraints\ValidDomainValidator', $this->getTestObject()
        );
    }

    /**
     * DataProvider for itShouldValidate
     */
    public function provideDomains()
    {
        return array(
            array(array('HIV'), 'example.hiv', false),
            array(array('HIV'), 'eXaMpLe.hIv', false),
            array(array('HIV'), 'example.tld', true),
            array(array(), 'example.tld', false)
        );
    }

    /**
     * @test
     * @depends      itShouldBeInstantiable
     * @dataProvider provideDomains
     *
     * @param array   $allowedTLDs Allowed TLDs.
     * @param string  $domainName  Name of the domain.
     * @param boolean $error       If the domain should be allowed.
     */
    public function itShouldValidate(array $allowedTLDs, $domainName, $error)
    {
        // Set up test data.
        $constraint          = new ValidDomain();
        $constraint->message = 'Test message';
        $domain              = new Domain();
        $domain->setName($domainName);

        // Set up expectations.
        if ($error) {
            $this->mockContext->expects($this->once())
                ->method('addViolation')
                ->with($constraint->message, array('{{ value }}' => $domain->getName()));
        } else {
            $this->mockContext->expects($this->never())
                ->method('addViolation');
        }

        $validator = $this->getTestObject();
        $validator->setAllowedTLDs($allowedTLDs);
        $validator->validate($domain, $constraint);
    }

    /**
     * @test
     * @depends                  itShouldValidate
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid TLD specification: "EXAMPLE.COM".
     */
    public function itShouldThrowExceptionOnInvalidSpecification()
    {
        $validator = $this->getTestObject();
        $validator->setAllowedTLDs('example.com');
    }

    protected function setUp()
    {
        // Set up mocks.
        $this->mockContext = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        $this->mockContext = null;
    }

    /**
     * @return ValidDomainValidator
     */
    protected function getTestObject()
    {
        $validator = new ValidDomainValidator();
        $validator->initialize($this->mockContext);
        return $validator;
    }

}
