<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
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

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return $this->catalog->useMinimalCatalog();
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
}
