<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\Integration\DatabaseSchemaHandler;
use Akeneo\Test\Integration\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractTestCase extends TestCase
{
    /** @var string */
    private static $edition;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        self::$edition = class_exists(
            'PimEnterprise\Bundle\WorkflowBundle\PimEnterpriseWorkflowBundle'
        ) ? 'enterprise' : 'community';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        $pimDir = 'ce' === self::$edition ? 'pim-community-dev' : 'pim-enterprise-dev';
        $installerBundlePrefix = 'ce' === self::$edition ? 'Pim' : 'PimEnterprise';
        $catalogDir = sprintf(
            '%s/../vendor/akeneo/%s/src/%s/Bundle/InstallerBundle/Resources/fixtures/minimal',
            $this->getParameter('kernel.root_dir'),
            $pimDir,
            $installerBundlePrefix
        );

        return new Configuration([$catalogDir]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixturesLoader(Configuration $configuration, DatabaseSchemaHandler $databaseSchemaHandler)
    {
        if ('enterprise' === self::$edition) {
            return new \Akeneo\TestEnterprise\Integration\FixturesLoader(
                static::$kernel,
                $configuration,
                $databaseSchemaHandler
            );
        }

        return parent::getFixturesLoader($configuration, $databaseSchemaHandler);
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
     * Launches a job instance
     *
     * @param string $jobCode
     *
     * @return int
     * @throws \Exception
     */
    protected function launch($jobCode)
    {
        $arrayInput = [
            'command'  => 'akeneo:batch:job',
            'code'     => $jobCode,
            '--no-log' => true,
            '-v'       => false,
        ];
        $input = new ArrayInput($arrayInput);
        $output = new BufferedOutput();
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        return $application->run($input, $output);
    }
}
