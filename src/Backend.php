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

declare(strict_types=1);

namespace Dotclear\Plugin\relatedLinks;

use Dotclear\Core\Backend\Menus;
use Dotclear\Core\Process;
use Dotclear\App;

class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (My::settings()->active) {
            My::addBackendMenuItem(Menus::MENU_BLOG);

            App::behavior()->addBehavior('adminPostForm', [Behaviors::class, 'adminPostForm']);
            App::behavior()->addBehavior('adminPostHeaders', [Behaviors::class, 'adminPostHeaders']);
            App::behavior()->addBehavior('adminAfterPostCreate', [Behaviors::class, 'setRelatedLinks']);
            App::behavior()->addBehavior('adminAfterPostUpdate', [Behaviors::class, 'setRelatedLinks']);
            App::behavior()->addBehavior('adminPopupPosts', [Behaviors::class, 'adminPopupPosts']);

            App::behavior()->addBehavior('initWidgets', [Widgets::class, 'initWidgets']);
            App::behavior()->addBehavior('initDefaultWidgets', [Widgets::class, 'initDefaultWidgets']);
        }

        return true;
    }
}
