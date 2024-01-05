# PIY Online plugin for Magento 2
This plugin will implement the PIY Online functionality in your Magento 2 store. It allows customers to add personalised ribbons to their orders, which can then be printed using the PIY Online Dashboard.

## Installation
You can install this plugin using Composer:
```
composer require piyribbons/magento2-piy-online
```

To enable the plugin in your store run the following commands:
```
bin/magento module:enable PiyRibbons_PiyOnline
bin/magento setup:upgrade
```

## Configuration
The plugin can be configured from your Magento 2 admin section by navigating to: Stores -> Configuration -> PIY Ribbons -> PIY Online

## License
MIT license. For more information, see the [LICENSE](LICENSE.txt) file.