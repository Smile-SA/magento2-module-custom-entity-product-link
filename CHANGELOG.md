# Changelog

All notable changes to this project will be documented in this file.

## [1.4.4] - 2024-02-14
[1.4.4]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.4.3...1.4.4

- Use ui-select input type for smile_custom_entity attributes

## [1.4.3] - 2024-01-09
[1.4.3]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.4.2...1.4.3

- Fix return type on the cart price rule creation BO page
- Fix error on attribute creation

## [1.4.3] - 2024-01-09
[1.4.3]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.4.2...1.4.3 

- Fix return type on the cart price rule creation BO page
- Fix error on attribute creation

## [1.4.2] - 2023-09-19
[1.4.2]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.4.1...1.4.2

- Fix explode php error

## [1.4.1] - 2023-09-19
[1.4.1]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.4.0...1.4.1

- Fix __construct for php 7.4

## [1.4.0] - 2023-09-19
[1.4.0]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.9...1.4.0

- From this release, product attribute linked to custom entities will not save their value in a custom table anymore.
  They will use the standard table of Magento (catalog_product_entity_text).
  This change makes possible to integrate these attributes in rule engine (promo, virtual category, optimizers),
  and allows to have different value for different stores.

## [1.3.9] - 2023-07-19
[1.3.9]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.8...1.3.9

- Add attribute id as catalog_product_custom_entity_link

## [1.3.8] - 2022-12-02
[1.3.8]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.7...1.3.8

- Fix Zend Db Expression

## [1.3.7] - 2022-11-21
[1.3.7]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.6...1.3.7

- Add github actions workflow

## [1.3.6] - 2022-10-19
[1.3.6]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.5...1.3.6

- Add Smile Lab Quality Suite
- Analyse code style with phpcs / phpmd / phpstan command
- Add db_schema.xml instead InstallSchema

## [1.3.5] - 2022-09-20
[1.3.5]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.4...1.3.5

- Fix return on _getSearchEntityIdsSql function on Model/ResourceModel/Search/CustomCollection.php

## [1.3.4] - 2022-09-14
[1.3.4]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.3...1.3.4

- Specify php version on composer.json
- Use Escaper on templates

## [1.3.3] - 2022-09-06
[1.3.3]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.2...1.3.3

- Add use for classes and use type hints on functions

## [1.3.2] - 2022-09-01
[1.3.2]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.1...1.3.2

- Add strict types on php files
- Remove disclaimer / annotation comments
- Add .gitattributes and update .gitignore

## [1.3.1] - 2022-08-23
[1.3.1]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.3.0...1.3.1

- Allow more recent version of custom-entity module to be used (Eg for Magento 2.4.4)
- Add CONTRIBUTING.md
- Update README.md

## [1.3.0] - 2020-10-01
[1.3.0]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.2.0...1.3.0

- Allow more recent version of custom-entity module to be used (Eg for Magento >=2.3.5)

## [1.2.1] - 2020-08-19
[1.2.1]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.2.0...1.2.1

- Update composer.json #25

## [1.2.0] - 2020-03-31
[1.2.0]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.1.0...1.2.0

- Update requirements of custom entities module

## [1.1.2] - 2020-03-27
[1.1.2]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.1.1...1.1.2

- Remove explicit PHP requirements.

## [1.1.1] - 2020-01-14
[1.1.1]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.1.0...1.1.1

- Fix the admin search (#23 thanks @maxcortyl)

## [1.1.0] - 2019-05-09
[1.1.0]: https://github.com/Smile-SA/magento2-module-custom-entity-product-link/compare/1.0.0...1.1.0

- Use latest ElasticSuite

## 1.0.0 - 2019-05-06

- Initial Release
