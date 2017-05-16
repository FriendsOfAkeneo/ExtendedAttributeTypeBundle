# ExtendedAttributeTypeBundle

Provides new attributes types for Akeneo PIM CE and EE:
- TextCollection: this attribute type can store an ordered collection of strings or URLs.

![Simple string collection](doc/img/string_collection.png)

## Requirements

| ExtendedAttributeTypeBundle | Akeneo PIM Community Edition |
|:---------------------------:|:----------------------------:|
| dev-master                  | v1.7.*                       |
| 1.0.*                       | v1.6.*                       |

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

## Configuration
You can read the detailed configuration documentation following thins link: [doc/index.md](doc/index.md)

## Contributing

If you want to contribute to this open-source project,
thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
