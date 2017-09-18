<?php

/**
 * This file is part of richardhj/contao-crossdomaincookies.
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package   CrossDomainCookies
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2015-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-crossdomaincookies/blob/master/LICENSE LGPL-3.0
 */

$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_legend'] = 'Cross domain cookies';

$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_handle_auth'][0]         = 'Share authentication cookies';
$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_handle_auth'][1]         ='By sharing the authentication cookies "FE_USER_AUTH" and "FE_AUTO_LOGIN" the member can stay logged in through the different domains.';
$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_activate_auto_login'][0] = 'Activate auto_login';
$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_activate_auto_login'][1] = 'Keep the "FE_AUTO_LOGIN" cookie, so the member will get logged in automatically despite he decided to or not.';
$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_shared_cookies'][0]      = 'Shared cookies';
$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_shared_cookies'][1]      = 'Type additional cookie names that should be shared cross-domain, e.g. "ISOTOPE_TEMP_CART".';
