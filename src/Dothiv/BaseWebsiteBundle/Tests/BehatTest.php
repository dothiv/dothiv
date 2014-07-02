<?php

namespace Dothiv\BaseWebsiteBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Runs behat tests inside the PHPUnit runner to enable coder coverage analysis.
 */
class BehatTest extends WebTestCase
{
    public function testThatBehatScenariosMeetAcceptanceCriteria()
    {
        $this->markTestSkipped('Not implemented yet.');

        $features = '@DothivBaseWebsiteBundle';

        try {
            $input  = new ArrayInput(array('--format' => 'progress', 'features' => $features));
            $output = new ConsoleOutput();

            $app = new \Behat\Behat\Console\BehatApplication('TEST');
            $app->setAutoExit(false);

            $result = $app->run($input, $output);

            $this->assertEquals(0, $result);
        } catch (\Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }
}
