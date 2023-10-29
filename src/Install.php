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
use Dotclear\Database\Structure;
use dcCore;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $new_version = dcCore::app()->plugins->moduleInfo(My::id(), 'version');
        $old_version = dcCore::app()->getVersion(My::id());

        if (version_compare((string) $old_version, $new_version, '>=')) {
            return true;
        }

        try {
            $s = new Structure(dcCore::app()->con, dcCore::app()->prefix);
            $s->related_link
            ->id ('bigint', 0, false)
            ->blog_id ('varchar', 32, false)
            ->post_id ('bigint', 0, false)
            ->link ('bigint', 0, false)
            ->position ('integer', 0, true)

            ->primary('pk_related_link', 'id');

            $s->related_link->reference('fk_related_link_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade');
            $s->related_link->reference('fk_related_link_post', 'post_id', 'post', 'post_id', 'cascade', 'cascade');

            $si = new Structure(dcCore::app()->con, dcCore::app()->prefix);
            $changes = $si->synchronize($s);

            My::settings()->put('active', false, 'boolean', 'Related Links plugin activated?', false);
            My::settings()->put('automatic_content', true, 'boolean', 'Add related links content automatically?', false);
            My::settings()->put('content_with_image', false, 'boolean', 'Add images to related links automatically?', false);
        } catch (\Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
