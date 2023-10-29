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

use Dotclear\Plugin\widgets\WidgetsStack;

class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        $w->create('related_links', 'Related Links', [Template::class, 'widget']);
        $w->related_links->setting('title', __('Title:'), __('Related Links'));
        $w->related_links->setting('content_only', __('Content only'), 0, 'check');
        $w->related_links->setting('class', __('CSS class:'), '');
    }

    public static function initDefaultWidgets(WidgetsStack $w, array $d): void
    {
        $d['extra']->append($w->related_links);
    }
}
