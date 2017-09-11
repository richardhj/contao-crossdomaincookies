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

use Contao\Environment;
use Contao\FrontendUser;
use Contao\PageModel;
use Contao\Input;

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
        $userId     = Input::get('u');

        if (null === $originHost || null === $linkToken || $linkToken !== $this->createLinkToken($userId)) {
            return;
        }

        //FIXME validate originhost

        $includeToken = $this->createIncludeToken($userId);

        $GLOBALS['TL_HEAD'][] = <<<HTML
<script src="$originHost/assets/crossdomaincookies/cookiemaker.php?t=$includeToken&u=$userId"></script>
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

        if ('link_open_switch' === $insertTagAction || 'link_url_switch' === $insertTagAction) {
            $targetPage = PageModel::findByIdOrAlias($insertTagTarget);

            if (null !== $targetPage) {
                $currentHost = Environment::get('url');
                $userId      = FrontendUser::getInstance()->id ?: 0;
                $linkToken   = $this->createLinkToken($userId);

                $href   = sprintf(
                    '%s?%s',
                    $targetPage->getFrontendUrl(),
                    http_build_query(['o' => $currentHost, 'u' => $userId, 't' => $linkToken])
                );
                $target = $targetPage->target
                    ? (('xhtml' === $targetPage->outputFormat) ? LINK_NEW_WINDOW : ' target="_blank"')
                    : '';
                $title  = ('' !== $targetPage->pageTitle) ? $targetPage->pageTitle : $targetPage->title;

                switch ($insertTagAction) {
                    case 'link_open_switch':
                        return sprintf('<a href="%s" title="%s"%s>', $href, specialchars($title), $target);

                    case 'link_url_switch':
                        return $href;
                }
            }
        }

        return false;
    }
}