<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
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

    /** @var DataLoader */
    private $dataLoader;

    /**
     * {@inheritdoc}
     */
    public static function getEdition()
    {
        if (null === self::$edition) {
            self::$edition = class_exists(
                'PimEnterprise\Bundle\WorkflowBundle\PimEnterpriseWorkflowBundle'
            ) ? 'enterprise' : 'community';
        }

        return self::$edition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        $pimDir = 'community' === self::getEdition() ? 'pim-community-dev' : 'pim-enterprise-dev';
        $installerBundlePrefix = 'community' === self::getEdition() ? 'Pim' : 'PimEnterprise';
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
        if ('enterprise' === self::getEdition()) {
            return new \Akeneo\TestEnterprise\Integration\FixturesLoader(
                static::$kernel,
                $configuration,
                $databaseSchemaHandler
            );
        }

        return parent::getFixturesLoader($configuration, $databaseSchemaHandler);
    }

    /**
     * @return DataLoader
     */
    protected function getDataLoader()
    {
        if (null === $this->dataLoader) {
            $this->dataLoader = new DataLoader(static::$kernel->getContainer());
        }

        return $this->dataLoader;
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
        $batchCommand = new BatchCommand();
        $application->add($batchCommand);
        $application->setAutoExit(false);
        $batchCommand->setContainer(static::$kernel->getContainer());

        return $application->run($input, $output);
    }
}
