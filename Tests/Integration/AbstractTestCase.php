<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTestCase extends KernelTestCase
{
    /** @var FixturesLoader */
    protected $fixturesLoader;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        static::bootKernel(['debug' => false]);
    }

    /**
     * @param string $service
     *
     * @return mixed
     */
    protected function get($service)
    {
        return static::$kernel->getContainer()->get($service);
    }

    /**
     * @param string $service
     *
     * @return mixed
     */
    protected function getParameter($service)
    {
        return static::$kernel->getContainer()->getParameter($service);
    }

    /**
     * Clears UOW + Cached repositories
     */
    protected function clear()
    {
        $this->get('doctrine.orm.entity_manager')->clear();
        $this->get('pim_catalog.object_manager.product')->clear();

        //TODO: Clear cached repositories
    }

    /**
     * @return FixturesLoader
     */
    protected function getFixturesLoader()
    {
        if (null === $this->fixturesLoader) {
            $this->fixturesLoader = new FixturesLoader(static::$kernel->getContainer());
        }
        return $this->fixturesLoader;
    }

    /**
     * Launches a job instance
     *
     * @param string $jobCode
     *
     * @return int
     */
    protected function launch($jobCode)
    {
        $arrayInput = [
            'command' => 'akeneo:batch:job',
            'code' => $jobCode,
            '--no-log' => true,
            '-v' => false
        ];
        $input = new ArrayInput($arrayInput);
        $output = new BufferedOutput();
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        return $application->run($input, $output);
    }
}
