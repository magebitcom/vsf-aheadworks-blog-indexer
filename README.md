# Aheadworks Blog indexer for Vue Storefront
This repository is a Magento 2 dependency for [Aheadworks Blog module for VSF](https://github.com/magebitcom/vsf-aheadworks-blog)

### Requirements
- [Aheadworks Blog](https://ecommerce.aheadworks.com/magento-2-extensions/blog)
- [magebit/vsbridge-static-content-procesor](https://github.com/magebitcom/static-content-processor) - See Installation section for details

### Installation

- Via composer: `composer require magebit/vsf-aheadworks-blog-indexer`
- Manually
    - Create a `Magebit` directory inside `app/code`
    - Clone this repository: `git clone git@github.com:magebitcom/vsf-aheadworks-blog-indexer.git`
    - This module also requires [magebit/vsbridge-static-content-procesor](https://github.com/magebitcom/static-content-processor)
    which you can install with composer `composer require magebit/vsbridge-static-content-procesor` or going to the repository and following manual installation steps there

### URL rewrites
By default, all indexed content and images will be with magento store urls. If you want to convert links and run images through VSF-API, you'll have to configure `Stores - Configuration - VueStorefront - Indexer - Static Content Processor` and check the "Enable URL Rewrites for aheadworks blog" option.

---

![Magebit](https://magebit.com/img/magebit-logo-2x.png)


## Authors

* **Emīls Malovka** (emils.malovka@magebit.com)


