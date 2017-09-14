# Contao CrossDomainCookies

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()
[![Dependency Status][ico-dependencies]][link-dependencies]

Cross-link between pages with different domain names of a Contao installation—and keep certain cookies alive. It will be 
possible to handle a member authentication between multiple domains. Or the isotope cart.

## Install

Via composer

```bash
$ composer require richardhj/contao-crossdomaincookies
```

## Usage

* Make sure that all domains used in the Contao installation are set in the "dns" field of the root pages.
* The user has to click a link in order to fetch the cookies of the site (he/she originally logged in). Include the link 
with an InsertTag.

## InsertTags

| InsertTag               | Description                                | Example                        |
| ----------------------- | ------------------------------------------ | ------------------------------ | 
| `{{link_url_cdc::99}}`  | Url to other site                          | `https://site-b.local?t=0000…` |
| `{{link_open_cdc::99}}` | Link opening tag linking to the other site | `<a href="…" title="…">`       |
| `{{link_close}}`        | Link closing tag (Contao core)             | `</a>`                         |

Make sure to replace `99` with the id or alias of the other page.

## How it works

Page A and Page B are part of one Contao installation.

When hyperlinking from Page A to Page B, the link looks like `https://page-b.local/?o=page-a.local&t=zyxitopjfsetbjjutwsdf`

As you can see, two get parameters are added to the page uri:

| Parameter | Role                                                                                            |
| --------- | ----------------------------------------------------------------------------------------------- |
| o         | The page redirected from and the origin of the cookies (where the cookies will be fetched from) |
| t         | A token, just for security purposes                                                             |

When being on Page B—and the get parameters are present—, a javascript will be included. This script will create the 
cookies on Page B. The script will get loaded from `https://page-a.local`, therefore the cookies are the ones present on 
Page A.

The javascript looks like
```js
document.cookie = "FE_USER_AUTH=; expires=Sun, 10 Sep 2017 17:34:31 GMT; path=/";
document.cookie = "FE_AUTO_LOGIN=abcdefghijklmnopqr; expires=Mon, 11 Dec 2017 17:34:31 GMT; path=/";
```

That's the magic behind cross domain cookies.

## Security

A few words about the security and vulnerability:

The extension automatically loads a javascript from a site that is given as a get parameter. The get parameter can be 
easily modified and this extension would be a great example for XSS-vulnerably in practice. So this extension needs to 
check that the url given as get parameter is part of the Contao installation. So before including the javascript from 
the other domain, it verifies that the other domain can be found in the "dns" fields of the root pages of the Contao 
installation.

In order to handle the authentication cross-domain, this extension activates auto_login ("Remember me") for the 
particular user. That means, that the user will not get logged out by closing the window, which is the default behavior. 
This is because Contao checks the auth cookie against the session_id which is bound to a cookie as well and will 
therefore change on the other domain. This might be a problem for websites with sensible data and users who forget to 
log out. Nevertheless this is a point that can be enhanced (for devs: either by enabling/disabling the auto_login cookie 
or by using the postAuth hook).

## License

The GNU Lesser General Public License (LGPL).

Feel free to contribute.

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-crossdomaincookies.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-crossdomaincookies/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-crossdomaincookies
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-crossdomaincookies
