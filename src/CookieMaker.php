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
use Contao\Input;
use Contao\MemberModel;
use Contao\SessionModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Cookie;
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
     * Handle all the cookies needed for cross-domain authentication
     *
     * @var bool
     */
    private $handleAuthentication;

    /**
     * The names of cookies to share cross-domain
     *
     * @var string[]
     */
    private $sharedCookies;

    /**
     * The cookies that will be printed in the javascript
     *
     * @var Cookie[]
     */
    private $cookies;

    /**
     * CookieMaker constructor.
     */
    public function __construct()
    {
        $this->handleAuthentication = Config::get('crossdomaincookies_handle_auth');
        $this->sharedCookies        = deserialize(Config::get('crossdomaincookies_shared_cookies'), true);
        $this->cookies              = [];
    }

    /**
     * Send javascript code which will load the cookies
     */
    public function handle()
    {
        $token = Input::get('t');

        $response = Response::create(null, Response::HTTP_OK, ['Content-Type' => 'application/javascript']);
        if ($token !== $this->createIncludeToken()) {
            $response->send();
            return;
        }

        if ($this->isHandleAuthentication()) {
            $this->handleAuthenticationCookies();
        }

        foreach ($this->getSharedCookies() as $cookieName) {
            $cookie = $this->createCookie($cookieName);
            $this->addCookie($cookie);
        }

        $cookiesJS =
            'document.cookie = "' . implode('";' . PHP_EOL . 'document.cookie = "', $this->getCookies()) . '";';
        $response->setContent($cookiesJS);
        $response->send();
    }

    /**
     * Handle authentication: Add cookies for FE_USER_AUTH and FE_AUTO_LOGIN
     */
    private function handleAuthenticationCookies()
    {
        $cookieName  = 'FE_USER_AUTH';
        $cookieValue = $this->getCookieValue($cookieName);
        // Will not work, see comment below
        //$cookie = $this->createCookie($cookieName);
        //$this->addCookie($cookie);

        if (null === ($sessionModel = SessionModel::findByHashAndName($cookieValue, $cookieName))) {
            return;
        }
        if (null === ($memberModel = MemberModel::findById($sessionModel->pid))) {
            return;
        }

        $cookieName   = 'FE_AUTO_LOGIN';
        $cookieValue  = $this->getCookieValue($cookieName);
        $cookieExpire = time() + $GLOBALS['TL_CONFIG']['autologin'];
        if (null === $cookieValue) {
            // Now we need to force auto_login as Contao checks for the session_id on regular authentication
            // and the session_id differs on both domains as well
            $currentIp     = Environment::get('ip');
            $sessionExpire = $sessionModel->tstamp + Config::get('sessionTimeout');

            // Validate the current session before we activate auto_login
            if ((!Config::get('disableIpCheck') && $currentIp !== $sessionModel->ip) || $sessionExpire < time()) {
                return;
            }

            $cookieValue = md5(uniqid(mt_rand(), true));

            $memberModel->createdOn = time();
            $memberModel->autologin = $cookieValue;
            $memberModel->save();

            // Equal rights for each domain. Set auto_login cookie on origin domain too
            System::setCookie($cookieName, $cookieValue, $cookieExpire);
        }

        $cookie = $this->createCookie($cookieName, $cookieValue, $cookieExpire);
        $this->addCookie($cookie);
    }

    /**
     * Create a cookie. Sets `httpOnly` to false by default (!)
     *
     * @param string                        $name     The name of the cookie
     * @param string|null                   $value    The value of the cookie
     * @param int|string|\DateTimeInterface $expire   The time the cookie expires
     * @param string                        $path     The path on the server in which the cookie will be available on
     * @param string|null                   $domain   The domain that the cookie is available to
     * @param bool                          $secure   Whether the cookie should only be transmitted over a secure HTTPS
     *                                                connection from the client
     * @param bool                          $httpOnly Whether the cookie will be made accessible only through the HTTP
     *                                                protocol
     * @param bool                          $raw      Whether the cookie value should be sent with no url encoding
     * @param string|null                   $sameSite Whether the cookie will be available for cross-site requests
     *
     * @return Cookie
     */
    private function createCookie(
        $name,
        $value = null,
        $expire = 0,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false,
        $raw = false,
        $sameSite = null
    ) {
        if (null === $value) {
            $value = $this->getCookieValue($name);
        }
        if (0 === $expire) {
            $time   = time();
            $expire = (null !== $value) ? $time + 2592000 : $time - 172800;
        }

        return new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * @return bool
     */
    public function isHandleAuthentication()
    {
        return $this->handleAuthentication;
    }

    /**
     * @return string[]
     */
    public function getSharedCookies()
    {
        return $this->sharedCookies;
    }

    /**
     * @return Cookie[]
     */
    private function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param Cookie $cookie
     */
    private function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    private function getCookieValue($name)
    {
        return Input::cookie($name);
    }
}
