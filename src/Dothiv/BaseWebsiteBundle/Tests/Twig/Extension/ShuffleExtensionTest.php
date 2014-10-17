<?php


namespace Dothiv\BaseWebsiteBundle\Tests\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Twig\Extension\ShuffleTwigExtension;

class ShuffleTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BaseWebsiteBundle
     * @group TwigExtension
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Twig\Extension\ShuffleTwigExtension', $this->getTestObject());
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @group        TwigExtension
     * @depends      itShouldBeInstantiable
     */
    public function itShouldShuffle()
    {
        $array  = array(
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling',
            'Every', 'day', 'I\'m', 'shuffling'
        );
        $before = join(' ', $array);
        $after  = join(' ', $this->getTestObject()->shuffle($array));
        $this->assertNotEquals($before, $after);
    }

    /**
     * @return ShuffleTwigExtension
     */
    protected function getTestObject()
    {
        return new ShuffleTwigExtension();
    }
}
