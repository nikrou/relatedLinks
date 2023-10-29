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
use dcCore;
use Dotclear\Helper\Html\Template\Template;

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
            dcCore::app()->tpl->addBlock('RelatedLinks', [Template::class, 'relatedLinks']);
            dcCore::app()->tpl->addBlock('RelatedLinksIf', [Template::class, 'relatedLinksIf']);
            dcCore::app()->tpl->addValue('RelatedLinkTitle', [Template::class, 'relatedLinkTitle']);
            dcCore::app()->tpl->addValue('RelatedLinkURL', [Template::class, 'relatedLinkURL']);
            dcCore::app()->tpl->addValue('RelatedLinkImage', [Template::class, 'relatedLinkImage']);

            if (My::settings()->automatic_content) {
                dcCore::app()->addBehavior('publicEntryAfterContent', [Behaviors::class, 'publicEntryAfterContent']);
            }

            dcCore::app()->addBehavior('initWidgets', [Widgets::class, 'initWidgets']);
            dcCore::app()->addBehavior('initDefaultWidgets', [Widgets::class, 'initDefaultWidgets']);
        }

        return true;
    }
}
