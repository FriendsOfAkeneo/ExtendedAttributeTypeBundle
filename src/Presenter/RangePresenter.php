<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\Presenter;

use Pim\Bundle\EnrichBundle\Resolver\LocaleResolver;
use Pim\Bundle\ExtendedAttributeTypeBundle\Localization\Presenter\RangePresenter as LocalizedRangePresenter;
use PimEnterprise\Bundle\WorkflowBundle\Presenter\AbstractProductValuePresenter;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangePresenter extends AbstractProductValuePresenter
{
    /** @var LocalizedRangePresenter */
    protected $rangePresenter;

    /** @var LocaleResolver */
    protected $localeResolver;

    /**
     * @param LocaleResolver $localeResolver
     */
    public function __construct(LocalizedRangePresenter $rangePresenter, LocaleResolver $localeResolver)
    {
        $this->rangePresenter = $rangePresenter;
        $this->localeResolver = $localeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsChange($attributeType)
    {
        return ExtendedAttributeTypes::RANGE === $attributeType;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeData($data)
    {
        if (null === $data) {
            return '';
        }

        $options         = ['locale' => $this->localeResolver->getCurrentLocale()];
        $structuredRange = ['max' => $data->getMax(), 'min' => $data->getMin()];

        return $this->rangePresenter->present($structuredRange, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeChange(array $change)
    {
        $options = ['locale' => $this->localeResolver->getCurrentLocale()];

        return $this->rangePresenter->present($change['data'], $options);
    }
}
