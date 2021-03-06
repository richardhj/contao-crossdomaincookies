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


$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'crossdomaincookies_handle_auth';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{crossdomaincookies_legend:hide},crossdomaincookies_handle_auth,crossdomaincookies_shared_cookies';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['crossdomaincookies_handle_auth'] = 'crossdomaincookies_activate_auto_login';

$GLOBALS['TL_DCA']['tl_settings']['fields']['crossdomaincookies_handle_auth']         = [
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_handle_auth'],
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12'
    ]
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['crossdomaincookies_activate_auto_login'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_activate_auto_login'],
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50 m12'
    ]
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['crossdomaincookies_shared_cookies']      = [
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['crossdomaincookies_shared_cookies'],
    'inputType' => 'listWizard',
    'eval'      => [
        'tl_class' => 'long clr'
    ]
];
