<?php
// +-----------------------------------------------------------------------+
// | related Links  - a plugin for Dotclear                                |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2010-2014 Nicolas Roudaire        http://www.nikrou.net  |
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

if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('initWidgets', array('relatedLinksWidgets', 'initWidgets'));
$core->addBehavior('initDefaultWidgets', array('relatedLinksWidgets', 'initDefaultWidgets'));

class relatedLinksWidgets
{
    public static function initWidgets($w) {
        $w->create('related_links', 'Related Links', array('tplRelatedLinks', 'widget'));
        $w->related_links->setting('title', __('Title:'), __('Related Links'));
    }

    public static function initDefaultWidgets($w, $d) {
        $d['extra']->append($w->related_links);
    }
}
