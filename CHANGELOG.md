# Changelog

## [2.5.0](https://github.com/DataLinx/php-utils/compare/v2.4.0...v2.5.0) (2024-07-10)


### Features

* **number:** add parameter for starting unit in asTimeInterval() ([c2c24be](https://github.com/DataLinx/php-utils/commit/c2c24be7b62cec2c96792815ed12c33a441e99c0))


### Miscellaneous Chores

* update .gitignore and add .idea config ([e721dfe](https://github.com/DataLinx/php-utils/commit/e721dfeab045bcc8d7308359cebdf27c25ac3dc6))

## [2.4.0](https://github.com/DataLinx/php-utils/compare/v2.3.0...v2.4.0) (2023-10-31)


### Features

* **datetime:** implement date and time parsing and formatting helper functions ([61c0830](https://github.com/DataLinx/php-utils/commit/61c083012deae7079659fccec80b68ca14776afc))
* **number:** implement number formatting as file size ([5d9674d](https://github.com/DataLinx/php-utils/commit/5d9674dffe7adccb88883f0fcd964266d4e18dba))
* **number:** implement number formatting as simple time interval ([2026774](https://github.com/DataLinx/php-utils/commit/20267741d2d195771c4cbaf60616238fc771b4c8))
* **number:** implement number parsing ([7c894cb](https://github.com/DataLinx/php-utils/commit/7c894cb70dc4d9a56bc45d5ad6b7035fee763f22))
* **number:** implement number, percent and money formatting ([f2c8fe5](https://github.com/DataLinx/php-utils/commit/f2c8fe558d28c807bf42495ab1bb8ed70b0c5524))
* **number:** improve internal type setting ([9b96cc0](https://github.com/DataLinx/php-utils/commit/9b96cc0962e407108b2c0766731ef51d96d4b344))
* **phone_number:** add fluent wrapper for libphonenumber ([ae23a97](https://github.com/DataLinx/php-utils/commit/ae23a9759769c6cc27d21c9a5348beab54acd4c8))
* **string:** add getLength, isEmpty and hasHtmlTags methods ([eccf21f](https://github.com/DataLinx/php-utils/commit/eccf21fd656abbf41fa22230ba1b4ede5728504c))

## [2.3.0](https://github.com/DataLinx/php-utils/compare/v2.2.0...v2.3.0) (2023-06-19)


### Features

* **array:** implement get method ([d5b4b00](https://github.com/DataLinx/php-utils/commit/d5b4b00b7290fc67e15ed5fae4a69d186e6582bc))

## [2.2.0](https://github.com/DataLinx/php-utils/compare/v2.1.0...v2.2.0) (2023-04-29)


### Features

* **string:** implement date and time placeholder parsing ([9a3bf9d](https://github.com/DataLinx/php-utils/commit/9a3bf9dcdc2d84a2e11341a9c7bf23a80b9507f1))


### Bug Fixes

* **string:** fix time placeholder parsing for PHP 7.4 ([c6e06bd](https://github.com/DataLinx/php-utils/commit/c6e06bd216ece2f76921a546edbbe3a7bea83745))

## [2.1.0](https://github.com/DataLinx/php-utils/compare/v2.0.0...v2.1.0) (2023-03-20)


### Features

* **string:** implement string chunking method ([32083b4](https://github.com/DataLinx/php-utils/commit/32083b45a524c915f8a59f679544c420bcbfd49c))

## [2.0.0](https://github.com/DataLinx/php-utils/compare/v1.2.0...v2.0.0) (2023-02-06)


### ⚠ BREAKING CHANGES

* **array:** renamd getArray method to toArray
* **array:** removed insertAfterKey, insertBefore, insertBeforeKey
* **string:** Merged (and deleted) Email helper into FluentString.

### Miscellaneous Chores

* fix export-ignore in .gitattributes ([d5f1f0e](https://github.com/DataLinx/php-utils/commit/d5f1f0e0cfa02cfe6d042b8003c083b6903b58fe))


### Code Refactoring

* **array:** improve FluentArray with more fluent methods ([f2e1e89](https://github.com/DataLinx/php-utils/commit/f2e1e8924a626978c5e7f84797e9c642860f4565))
* **array:** renamed getArray method to toArray ([a66374e](https://github.com/DataLinx/php-utils/commit/a66374e30c64bb959a63d18159dc4f186948c7aa))
* **string:** move email domain validation to FluentString ([89485b0](https://github.com/DataLinx/php-utils/commit/89485b037367686f6086c7ecff1abb75f592999d))

## [1.2.0](https://github.com/DataLinx/php-utils/compare/v1.1.0...v1.2.0) (2023-01-16)


### Features

* add string trimming function that includes unicode spaces ([fb7c909](https://github.com/DataLinx/php-utils/commit/fb7c90952f5e56055ec74272699f1456cbf35998))

## [1.1.0](https://github.com/DataLinx/php-utils/compare/v1.0.0...v1.1.0) (2022-12-17)


### Features

* **string:** add multibyte-safe method for uppercasing a string's first char ([ff87e61](https://github.com/DataLinx/php-utils/commit/ff87e61a88127edde2af2aff42eecc3b521d0f84))

## 1.0.0 (2022-12-17)

Initial release
