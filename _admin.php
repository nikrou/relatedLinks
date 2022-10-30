<?php
/*
 * This file is part of relatedLinks plugin, for dotclear
 *
 * Copyright(c) Nicolas Roudaire  https://www.nikrou.net/
 * Licensed under the GPL version 2.0 license.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

dcCore::app()->menu[dcAdmin::MENU_PLUGINS]->addItem(
    'Related Links',
    'plugin.php?p=relatedLinks',
    'index.php?pf=relatedLinks/img/icon.png',
    preg_match('/plugin.php\?p=relatedLinks/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_USAGE, dcAuth::PERMISSION_CONTENT_ADMIN]), dcCore::app()->blog->id)
);

dcCore::app()->blog->settings->addNameSpace('relatedlinks');
if (dcCore::app()->blog->settings->relatedlinks->active) {
    dcCore::app()->addBehavior('adminPostForm', [relatedLinksBehaviors::class, 'adminPostForm']);
    dcCore::app()->addBehavior('adminPostHeaders', [relatedLinksBehaviors::class, 'adminPostHeaders']);
    dcCore::app()->addBehavior('adminAfterPostCreate', [relatedLinksBehaviors::class, 'setRelatedLinks']);
    dcCore::app()->addBehavior('adminAfterPostUpdate', [relatedLinksBehaviors::class, 'setRelatedLinks']);
}

include(__DIR__ . '/_widgets.php');
