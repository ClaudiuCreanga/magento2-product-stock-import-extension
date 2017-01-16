# Magento2 product-stock import extension (via cron or manually)

## Goal

* This extension is intended primarily for stores that need to receive stock and product data from third parties (warehouse) **via files** (not API).

## Install

```
$ composer require claudiucreanga/magento2-import
$ composer update
$ php bin/magento setup:upgrade 
$ php bin/magento setup:static-content:deploy
$ php bin/magento cache:clean
```
Or you can download the latest version from here https://github.com/ClaudiuCreanga/magento2-product-stock-import-extension/releases/, drop it into your app folder, copy the contents of src folder into the main folder, change composer.json registration path and then run the last 2 commands above.

Or you can also get it from the <a href="https://marketplace.magento.com/limesharp-stockists.html"> magento2 marketplace.</a>

It requires magento 2.1 or above and php7

## Features
* Advanced logging (admin import integration and /var/log/import_integration.log)

## Usage

## Flow
* If there is no product with the given sku then a new product is created. If the sku is already in the database then we will update the product details with the new data from file. 

## Support
* I **DO NOT** offer any free technical support in installing or customizing this extension.
* This extention works out of the box with any magento 2.1 site, but depending on your configuration it may need further work (especially regarding custom attributes).
* If you need help please ask questions on http://magento.stackexchange.com/ .

## Demo
