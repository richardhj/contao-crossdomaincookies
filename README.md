# Contao CrossDomainCookies

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()
[![Dependency Status][ico-dependencies]][link-dependencies]

Cross-link between pages with different domain names of a Contao installation — and keep certain cookies alive. It will be possible to handle a member authentication between multiple domains. Or the isotope cart.

## Usage:

* Make sure that all domains used in the Contao installation are set in the "dns" field in the root pages
* The user has to click a link in order to fetch the cookies of the site (he originally logged in). Include the link with an InsertTag

## InsertTags:

* `{{link_url_cdc::99}}`: Url to other site
* `{{link_open_cdc::99}}`: The complete `<a>` tag linking to the other site
* `{{link_close}}`: `</a>` (Contao core)

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-crossdomaincookies.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-crossdomaincookies/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-crossdomaincookies
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-crossdomaincookies
