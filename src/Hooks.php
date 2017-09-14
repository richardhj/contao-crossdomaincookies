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

use Contao\Database;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\PageModel;
use Contao\Input;
use Contao\User;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LogEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;


/**
 * Class Hooks
 *
 * @package Richardhj\Contao\CrossDomainCookies
 */
class Hooks
{

    use CreateTokenTrait;

    /**
     * Look for identifier in the get parameters and add JS script to the page layout which will set the cookies
     *
     * @internal param PageModel $objPage
     * @internal param LayoutModel $objLayout
     * @internal param PageRegular $objPageRegular
     */
    public function cookieListener()
    {
        $originHost = Input::get('o');
        $linkToken  = Input::get('t');

        if (null === $originHost || null === $linkToken || $linkToken !== $this->createLinkToken()) {
            return;
        }
        if (null === ($activePageRoots = PageModel::findPublishedRootPages())) {
            return;
        }

        // Check that the origin is part of this Contao installation (possible XSS vulnerability)
        $validDns = $activePageRoots->fetchEach('dns');
        if (false === in_array($originHost, $validDns)) {
            $this->getEventDispatcher()->dispatch(
                ContaoEvents::SYSTEM_LOG,
                new LogEvent(
                    sprintf(
                        'Will not include crossdomaincokie-script from "%s" as it cannot be found in any dns settings of active root pages.',
                        $originHost
                    ), __METHOD__, TL_ERROR
                )
            );
            return;
        }

        $includeToken         = $this->createIncludeToken();
        $GLOBALS['TL_HEAD'][] = <<<HTML
<script src="//$originHost/assets/crossdomaincookies/cookiemaker.php?t=$includeToken"></script>
HTML;
    }


    /**
     * Replace insert tags
     *
     * @param string $insertTag
     *
     * @return string|false
     */
    public function replaceSwitchInsertTags($insertTag)
    {
        $elements = explode('::', $insertTag);
        list ($insertTagAction, $insertTagTarget) = $elements;

        if ('link_open_cdc' === $insertTagAction || 'link_url_cdc' === $insertTagAction) {
            $targetPage = PageModel::findByIdOrAlias($insertTagTarget);

            if (null !== $targetPage) {
                $currentHost = Environment::get('httpHost');
                $linkToken   = $this->createLinkToken();

                $href = sprintf(
                    '%s?%s',
                    $targetPage->getFrontendUrl(),
                    http_build_query(['o' => $currentHost, 't' => $linkToken])
                );

                switch ($insertTagAction) {
                    case 'link_open_cdc':
                        $target = $targetPage->target
                            ? (('xhtml' === $targetPage->outputFormat) ? LINK_NEW_WINDOW : ' target="_blank"')
                            : '';
                        $title  = ('' !== $targetPage->pageTitle) ? $targetPage->pageTitle : $targetPage->title;
                        return sprintf('<a href="%s" title="%s"%s>', $href, specialchars($title), $target);

                    case 'link_url_cdc':
                        return $href;
                }
            }
        }

        return false;
    }

    /**
     * The default logout routine deletes the current session entry and expires the auth cookie. This logs out the user
     * on the current domain exclusively.
     * We widen the logout by truncating the auto_login hash for the member and deleting all session entries for the
     * member.
     *
     * @param User $user
     */
    public function forceLogout(User $user)
    {
        if (!$user instanceof FrontendUser) {
            return;
        }

        $user->autologin = '';
        $user->save();

        Database::getInstance()
            ->prepare("DELETE FROM tl_session WHERE pid=? AND name='FE_USER_AUTH'")
            ->execute($user->id);
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }
}
