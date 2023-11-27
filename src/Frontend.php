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

use Dotclear\Core\Process;
use Dotclear\App;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (My::settings()->active) {
            App::frontend()->template()->addBlock('RelatedLinks', [Template::class, 'relatedLinks']);
            App::frontend()->template()->addBlock('RelatedLinksIf', [Template::class, 'relatedLinksIf']);
            App::frontend()->template()->addValue('RelatedLinkTitle', [Template::class, 'relatedLinkTitle']);
            App::frontend()->template()->addValue('RelatedLinkURL', [Template::class, 'relatedLinkURL']);
            App::frontend()->template()->addValue('RelatedLinkImage', [Template::class, 'relatedLinkImage']);

            if (My::settings()->automatic_content) {
                App::behavior()->addBehavior('publicEntryAfterContent', [Behaviors::class, 'publicEntryAfterContent']);
            }

            App::behavior()->addBehavior('initWidgets', [Widgets::class, 'initWidgets']);
            App::behavior()->addBehavior('initDefaultWidgets', [Widgets::class, 'initDefaultWidgets']);
        }

        return true;
    }
}
