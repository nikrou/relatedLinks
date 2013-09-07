<?php
// +-----------------------------------------------------------------------+
// | related Links  - a plugin for Dotclear                                |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2010-2013 Nicolas Roudaire        http://www.nikrou.net  |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License version 2 as     |
// | published by the Free Software Foundation                             |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,            |
// | MA 02110-1301 USA.                                                    |
// +-----------------------------------------------------------------------+

$version = $core->plugins->moduleInfo('relatedLinks', 'version');
if (version_compare($core->getVersion('relatedLinks'), $version,'>=')) {
  return;
}

$settings = $core->blog->settings;
$settings->addNamespace('relatedlinks');

$settings->relatedlinks->put('active', false, 'boolean', 'Related Links plugin activated?', false);
$settings->relatedlinks->put('automatic_content', true, 'boolean', 'Add related Links content automatically?', false);

$s = new dbStruct($core->con, $core->prefix);
$s->related_link
->id ('bigint',	0, false)
->blog_id ('varchar', 32, false)
->post_id ('bigint', 0,	false)
->link ('bigint', 0, false)
->position ('integer', 0, true)

->primary('pk_related_link', 'id');

$s->related_link->reference('fk_related_link_blog','blog_id','blog','blog_id','cascade','cascade');
$s->related_link->reference('fk_related_link_post','post_id','post','post_id','cascade','cascade');

$si = new dbStruct($core->con,$core->prefix);
$changes = $si->synchronize($s);

$core->setVersion('relatedLinks', $version);
return true;
