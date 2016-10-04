# ExtendedAttributeTypeBundle

Provides new attributes types for Akeneo PIM CE and EE:
- Range

## Requirements

| ExtendedAttributeTypeBundle | Akeneo PIM Community Edition |
|:---------------------------:|:----------------------------:|
| dev-master                  | v1.5.*                       |

## Installation
You can install this bundle with composer (see requirements section):


Add the following bundle in your `app/AppKernel.php` file:

```php
$bundles = [
    new Pim\Bundle\ExtendedAttributeTypeBundle\PimExtendedAttributeTypeBundle(),
];
```

You need to create a bundle to make the glue between this bundle and your application. Here the bundle is `Acme\Bundle\AppBundle`:

### Override the product value (CE)

Override the product value class:
```php
<?php

namespace Acme\Bundle\AppBundle\Model\ProductValue;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeValueTrait;
use Pim\Component\Catalog\Model\ProductValue as PimProductValue;

class ProductValue extends PimProductValue
{
    use RangeValueTrait;
}
```

Then do not forget to override the entity parameter:
```yaml
parameters:
    pim_catalog.entity.product_value.class: Acme\Bundle\AppBundle\Model\ProductValue
```

In the `app/config.yml` file, add the following lines:

```yaml
akeneo_storage_utils:
    mapping_overrides:
        -
            original: Pim\Component\Catalog\Model\ProductValue
            override: Acme\Bundle\AppBundle\Entity\ProductValue
```

***ORM***
The ORM mapping:
```yaml
Acme\Bundle\AppBundle\Model\ProductValue:
    type: entity
    table: pim_catalog_product_value
    changeTrackingPolicy: DEFERRED_EXPLICIT
    indexes:
        value_idx:
            columns:
                - attribute_id
                - locale_code
                - scope_code
        varchar_idx:
            columns:
                - value_string
        integer_idx:
            columns:
                - value_integer
    oneToOne:
        range:
            targetEntity: Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange
            cascade:
                - remove
                - persist
                - refresh
                - detach
            inversedBy: value
            joinColumns:
                range_id:
                    referencedColumnName: id
                    onDelete: 'SET NULL'
```

***MongoDB***
```yaml
Acme\Bundle\AppBundle\Model\ProductValue:
    type: embeddedDocument
    fields:
        range:
            embedded: true
            type: one
            targetEntity: Pim\Bundle\ExtendedAttributeTypeBundle\Model\ProductRange
```


### Override the product value and the published product value (EE)

Override the product value class:

```php
<?php

namespace Acme\Bundle\AppBundle\Model\ProductValue;

use Pim\Bundle\ExtendedAttributeTypeBundle\Model\RangeValueTrait;
use PimEnterprise\Bundle\CatalogBundle\Model\ProductValue as PimProductValue;

class ProductValue extends PimProductValue
{
    use RangeValueTrait;
}
```

Then do not forget to override the entity parameter:
```yaml
parameters:
    pim_catalog.entity.product_value.class: Acme\Bundle\AppBundle\Model\ProductValue
```


***ORM***
You can see the mapping for the product value above.

***MongoDB***
You can see the mapping for the product value above.

## Documentation


## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
