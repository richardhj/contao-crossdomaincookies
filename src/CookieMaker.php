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

namespace Richardhj\Contao\CrossDomainCookies;

use Contao\Database;
use Contao\Input;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CookieMaker
 *
 * @package Richardhj\Contao\CrossDomainCookies
 */
class CookieMaker
{

    use CreateTokenTrait;


    /**
     * Send javascript code which will load the cookies
     */
    public function handle()
    {
        $return = '';
        $token  = Input::get('t');
        $userId = Input::get('u');

        $response = Response::create(null, Response::HTTP_OK, ['Content-Type' => 'application/javascript']);
        if ($token !== $this->createIncludeToken($userId)) {
            $response->send();
            return;
        }

        // These are the cookies which will be included in the site
        // TODO configurable cookie names via tl_settings
        $cookieNames = [
            'ISOTOPE_TEMP_CART',
            'FE_USER_AUTH',
            'FE_AUTO_LOGIN'
        ];

        $setAutoLogin = false;
        foreach ($cookieNames as $cookieName) {
            $time          = time();
            $cookieContent = Input::cookie($cookieName);
            $timeSet       = $cookieContent ? $time + 2592000 : $time - 172800;

            if ('FE_AUTO_LOGIN' === $cookieName && true === $setAutoLogin && $userId) {
                $token = md5(uniqid(mt_rand(), true));

                $set['createdOn'] = $time;
                $set['autologin'] = $token;
                Database::getInstance()
                    ->prepare("UPDATE tl_member %s WHERE id=?")
                    ->set($set)
                    ->execute($userId);

                $cookieContent = $token;
                $timeSet       = $time + $GLOBALS['TL_CONFIG']['autologin'];
            }

            if ('FE_USER_AUTH' === $cookieName && '' !== $cookieContent) {
                $setAutoLogin = true;
            }

            $cookieExpires = gmdate(
                'D, d M Y H:i:s T',
                $timeSet
            );

            $return .= <<<JS
document.cookie = "$cookieName=$cookieContent; expires=$cookieExpires; path=/";\n
JS;
        }

        $response->setContent($return);
        $response->send();
    }
}
