# ExtendedAttributeTypeBundle

Provides new attributes types for Akeneo PIM CE and EE:
- Range

## Requirements

| ExtendedAttributeTypeBundle | Akeneo PIM Community Edition |
|:---------------------------:|:----------------------------:|
| dev-master                  | v1.6.*                       |

## Installation
You can install this bundle with composer (see requirements section).

First, add the VCS then launch the following command from your root application:
```
    php composer.phar require --prefer-dist akeneo/extended-attribute-type:dev-master
```

Add the following bundle in your `app/AppKernel.php` file:

```php
$bundles = [
    new Pim\Bundle\ExtendedAttributeTypeBundle\PimExtendedAttributeTypeBundle(),
];
```

If you are using the CE, you can find needed classes in `doc/example/Acme/Bundle/AppBundle`.
If you are using the EE, you can find needed classes in `doc/example/Acme/Bundle/AppEEBundle`.

Feel free to create a symbolic link to these resources in your src directory and add the bundle in your `app/AppKernel.php` file.

## Documentation


## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
