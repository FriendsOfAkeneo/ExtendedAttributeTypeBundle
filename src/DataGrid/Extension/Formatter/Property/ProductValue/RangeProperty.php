<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DataGrid\Extension\Formatter\Property\ProductValue;

use Akeneo\Component\Localization\Presenter\PresenterInterface;
use Pim\Bundle\DataGridBundle\Extension\Formatter\Property\ProductValue\TwigProperty;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Formatter for Range attribute type, to get the value in the product data grid.
 * Works with a twig template (Resources/views/Property/range.html.twig).
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class RangeProperty extends TwigProperty
{
    /** @var PresenterInterface */
    protected $presenter;

    /**
     * @param \Twig_Environment   $environment
     * @param TranslatorInterface $translator
     * @param PresenterInterface  $presenter
     */
    public function __construct(
        \Twig_Environment $environment,
        TranslatorInterface $translator,
        PresenterInterface $presenter
    ) {
        parent::__construct($environment);
        $this->translator = $translator;
        $this->presenter  = $presenter;
    }

    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        $result = $this->getBackendData($value);

        return $this->presenter->present($result, ['locale' => $this->translator->getLocale()]);
    }
}
