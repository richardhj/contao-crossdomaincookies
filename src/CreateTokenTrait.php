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

namespace Richardhj\Contao\CrossDomainCookies;


use Contao\Config;
use Contao\Environment;

trait CreateTokenTrait
{

    private function createIncludeToken($userId)
    {
        return $this->createToken($userId, 'include');
    }

    private function createLinkToken($userId)
    {
        return $this->createToken($userId, 'link');
    }

    private function createToken($userId, $action)
    {
        $ip = (!Config::get('disableIpCheck')) ? Environment::get('ip') : '';

        return md5(
            http_build_query(
                [
                    'act'  => $action,
                    'user' => (int)$userId,
                    'ip'   => $ip,
                    'enc'  => Config::get('encryptionKey')
                ]
            )
        );
    }
}