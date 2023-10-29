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

use dcCore;
use Dotclear\Core\Process;

class Prepend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::PREPEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->rest->addFunction('addRelatedLink', [RestMethods::class, 'addRelatedLink']);
        dcCore::app()->rest->addFunction('removeRelatedLink', [RestMethods::class, 'removeRelatedLink']);
        dcCore::app()->rest->addFunction('removeRelatedLinks', [RestMethods::class, 'removeRelatedLinks']);
        dcCore::app()->rest->addFunction('getRelatedLinks', [RestMethods::class, 'getRelatedLinks']);

        return true;
    }
}
