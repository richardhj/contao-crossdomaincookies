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

namespace Richardhj\Contao\CrossDomainCookies;

use Contao\Config;
use Contao\Environment;


/**
 * Trait CreateTokenTrait
 *
 * @package Richardhj\Contao\CrossDomainCookies
 */
trait CreateTokenTrait
{

    /**
     * @return string
     */
    private function createIncludeToken()
    {
        return $this->createToken('include');
    }

    /**
     * @return string
     */
    private function createLinkToken()
    {
        return $this->createToken('link');
    }

    /**
     * @param string $action
     *
     * @return string
     */
    private function createToken($action)
    {
        $ip = (!Config::get('disableIpCheck')) ? Environment::get('ip') : '';

        return md5(
            http_build_query(
                [
                    'act' => $action,
                    'ip'  => $ip,
                    'enc' => Config::get('encryptionKey')
                ]
            )
        );
    }
}
