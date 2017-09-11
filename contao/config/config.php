<?php

/**
 * This file is part of richardhj/contao-crossdomaincookies.
 *
 * Copyright (c) 2017 Richard Henkenjohann
 *
 * @package   CrossDomainCookies
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2017 Richard Henkenjohann
 * @license   https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0
 */


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['generatePage'][]      = ['Richardhj\Contao\CrossDomainCookies\Hooks', 'cookieListener'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['Richardhj\Contao\CrossDomainCookies\Hooks', 'replaceSwitchInsertTags'];
