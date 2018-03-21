<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Akeneo\Bundle\StorageUtilsBundle\DependencyInjection\AkeneoStorageUtilsExtension;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\Integration\DatabaseSchemaHandler;
use Akeneo\Test\Integration\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
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

    protected function setUp()
    {
        parent::setUp();
        $storage = $this->getParameter('pim_catalog_product_storage_driver');
        if (AkeneoStorageUtilsExtension::DOCTRINE_MONGODB_ODM === $storage) {
            $this->resetMongo();
        }
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
     * Clears UOW + Cached repositories + Validation statuses
     */
    protected function clear()
    {
        $this->get('doctrine.orm.entity_manager')->clear();
        $this->get('pim_catalog.object_manager.product')->clear();

        $this->get('pim_catalog.repository.cached_attribute')->clear();
        $this->get('pim_catalog.repository.cached_attribute_option')->clear();
        $this->get('pim_catalog.repository.cached_category')->clear();
        $this->get('pim_catalog.repository.cached_channel')->clear();
        $this->get('pim_catalog.repository.cached_family')->clear();
        $this->get('pim_catalog.repository.cached_locale')->clear();

        $this->get('pim_catalog.validator.unique_value_set')->reset();
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

    /**
     * Drops and recreate the MongoDB database schema.
     *
     * @throws \RuntimeException
     */
    private function resetMongo()
    {
        $cli = new Application(static::$kernel);
        $cli->setAutoExit(false);
        $input = new ArrayInput(
            [
                'command' => 'doctrine:mongodb:schema:drop',
                '--env'   => 'test',
            ]
        );
        $output = new BufferedOutput();
        $exitCode = $cli->run($input, $output);
        if (0 !== $exitCode) {
            throw new \RuntimeException(
                sprintf('Impossible to drop the MongoDB database schema! "%s"', $output->fetch())
            );
        }
        $input = new ArrayInput(
            [
                'command' => 'doctrine:mongodb:schema:create',
                '--env'   => 'test',
            ]
        );
        $output = new BufferedOutput();
        $exitCode = $cli->run($input, $output);

        if (0 !== $exitCode) {
            throw new \RuntimeException(
                sprintf('Impossible to create the MongoDB database schema! "%s"', $output->fetch())
            );
        }
    }
}
