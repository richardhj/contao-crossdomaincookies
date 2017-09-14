<?php

/**
 * This file is part of richardhj/contao-crossdomaincookies.
 *
 * Copyright (c) 2017 Richard Henkenjohann
 *
 * @package   CrossDomainCookies
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-crossdomaincookies/blob/master/LICENSE LGPL-3.0
 */


/**
 * Default settings
 */
$GLOBALS['TL_CONFIG']['crossdomaincookies_handle_auth'] = true;
if (in_array('isotope', \Contao\ModuleLoader::getActive())) {
    $GLOBALS['TL_CONFIG']['crossdomaincookies_shared_cookies'] = ['ISOTOPE_TEMP_CART'];
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['generatePage'][]      = ['Richardhj\Contao\CrossDomainCookies\Hooks', 'cookieListener'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['Richardhj\Contao\CrossDomainCookies\Hooks', 'replaceSwitchInsertTags'];
$GLOBALS['TL_HOOKS']['postLogout'][]        = ['Richardhj\Contao\CrossDomainCookies\Hooks', 'forceLogout'];
