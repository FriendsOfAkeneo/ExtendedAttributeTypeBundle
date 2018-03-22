<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Tests\Integration;

use Akeneo\Component\Batch\Model\JobInstance;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class DataLoader
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @return ProductInterface
     */
    public function createProduct($identifier, array $data)
    {
        $product = $this->container->get('pim_catalog.builder.product')->createProduct($identifier);
        $this->container->get('pim_catalog.updater.product')->update($product, $data);
        $constraintViolations = $this->container->get('pim_catalog.validator.product')->validate($product);
        if (count($constraintViolations) > 0) {
            throw new \Exception(sprintf('Product "%s" is not valid', $product));
        }
        $this->container->get('pim_catalog.saver.product')->save($product);
        sleep(2);
        return $product;
    }

    /**
     * @param array $data
     *
     * @return AttributeInterface
     */
    public function createAttribute(array $data)
    {
        if (!isset($data['group'])) {
            $defaultGroup = $this->container->get('pim_catalog.repository.attribute_group')
                                ->findDefaultAttributeGroup();
            $data['group'] = $defaultGroup->getCode();
        }

        $attribute = $this->container->get('pim_catalog.factory.attribute')->createAttribute();
        $this->container->get('pim_catalog.updater.attribute')->update($attribute, $data);
        $constraintViolations = $this->container->get('validator')->validate($attribute);

        if (count($constraintViolations) > 0) {
            throw new \Exception(sprintf('Attribute "%s" is not valid', $attribute));
        }
        $this->container->get('pim_catalog.saver.attribute')->save($attribute);

        return $attribute;
    }

    /**
     * @param array $data
     *
     * @return FamilyInterface
     */
    public function createFamily(array $data)
    {
        $family = $this->container->get('pim_catalog.factory.family')->create();
        $this->container->get('pim_catalog.updater.family')->update($family, $data);
        $constraintViolations = $this->container->get('validator')->validate($family);
        if (count($constraintViolations) > 0) {
            throw new \Exception(sprintf('Family "%s" is not valid', $family));
        }
        $this->container->get('pim_catalog.saver.family')->save($family);

        return $family;
    }

    /**
     * @param string[] $locales
     */
    public function activateLocales($locales = [])
    {
        /** @var ChannelRepositoryInterface $channelRepo */
        $channelRepo = $this->container->get('pim_catalog.repository.channel');
        /** @var ChannelInterface $defaultScope */
        $defaultScope = $channelRepo->findOneByIdentifier('ecommerce');
        $activeLocales = $defaultScope->getLocaleCodes();
        foreach ($locales as $localeName) {
            $activeLocales[] = $localeName;
        }
        $this->container->get('pim_catalog.updater.channel')->update($defaultScope, ['locales' => array_unique($activeLocales)]);
        $this->container->get('pim_catalog.saver.channel')->save($defaultScope);
    }

    /**
     * @param string $jobCode
     * @param string $type
     * @param array $config
     */
    public function createJobInstance($jobCode, $type, $config = [])
    {
        $job = new JobInstance();
        $job->setCode($jobCode);
        $job->setType($type);
        $job->setLabel($jobCode);
        $job->setConnector('Akeneo CSV Connector');
        $job->setJobName($jobCode);
        $job->setRawParameters($config);
        $this->container->get('akeneo_batch.saver.job_instance')->save($job);
    }

    /**
     * @param array $data
     */
    public function createChannel(array $data)
    {
        $channel = $this->container->get('pim_catalog.factory.channel')->create();

        $this->container->get('pim_catalog.updater.channel')->update($channel, $data);
        $this->container->get('pim_catalog.saver.channel')->save($channel);
    }
}
