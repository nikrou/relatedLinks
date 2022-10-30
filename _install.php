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

$version = dcCore::app()->plugins->moduleInfo('relatedLinks', 'version');
if (version_compare(dcCore::app()->getVersion('relatedLinks'), $version, '>=')) {
    return;
}

$settings = dcCore::app()->blog->settings;
$settings->addNamespace('relatedlinks');

$settings->relatedlinks->put('active', false, 'boolean', 'Related Links plugin activated?', false);
$settings->relatedlinks->put('automatic_content', true, 'boolean', 'Add related links content automatically?', false);
$settings->relatedlinks->put('content_with_image', false, 'boolean', 'Add images to related links automatically?', false);

$s = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);
$s->related_link
->id ('bigint', 0, false)
->blog_id ('varchar', 32, false)
->post_id ('bigint', 0, false)
->link ('bigint', 0, false)
->position ('integer', 0, true)

->primary('pk_related_link', 'id');

$s->related_link->reference('fk_related_link_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade');
$s->related_link->reference('fk_related_link_post', 'post_id', 'post', 'post_id', 'cascade', 'cascade');

$si = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);
$changes = $si->synchronize($s);

dcCore::app()->setVersion('relatedLinks', $version);
return true;
