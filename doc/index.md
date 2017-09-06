# ExtendedAttributeTypeBundle

You will find all custom files in the `doc/example` directory.
Feel free to create a symbolic link to these resources in your src directory and add the bundle in your `app/AppKernel.php` file.

## Data model override
We need to override the PIM product value to add the to add the new textCollection backend type.

### Create the new ProductValue model
Firstly, we will define the new `ProductValue` class in our custom AppBundle. To ease the integration,
the extension provides a Trait to avoid copy-paste.
The resulting ProductValue should look like the example: [AppBundle/Model/ProductValue.php](example/Pim/Bundle/ExtendedCeBundle/Model/ProductValue.php)

### Override the namespace
We then need to override the Doctrine namespace to use this new definition.
In `app/config/config.yml`, modify the configuration of the `akeneo_storage_utils` section:

```yml
akeneo_storage_utils:
    mapping_overrides:
        -
            original: Pim\Component\Catalog\Model\ProductValue
            override: Acme\Bundle\AppBundle\Model\ProductValue
```

### Override the Doctrine definition
Now that the namespace is redefined, we will configure this model to add our new field.
Create the `ProductValue` overridden configuration in [AppBundle/Resources/config/model/doctrine/ProductValue.orm.yml](example/Pim/Bundle/ExtendedCeBundle/Resources/config/model/doctrine/ProductValue.orm.yml):

```php
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
    fields:
        textCollection:
            type: json_array
            nullable: true
            column: value_text_collection
```

### Update schema
We will apply all these modifications to the database schema. In a development environment, you can use the
doctrine command `app/console doctrine:schema:update --dump-sql` to check the modifications, 
and then `app/console doctrine:schema:update --force` to apply them. n a production environment, 
you should rely on migrations or other safer migrations scripts. 

### Clear cache and regenerate assets
Lastly, we need to clear the cache and regen assets to access all new classes and JS files: 
`app/console cache:clear; app/console pim:installer:assets` (with `--env=prod` for a prod environment).
