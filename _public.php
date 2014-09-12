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

$core->blog->settings->addNameSpace('relatedlinks');
if ($core->blog->settings->relatedlinks->active) {
    $core->tpl->addBlock('RelatedLinks', array('tplRelatedLinks','relatedLinks'));
    $core->tpl->addBlock('RelatedLinksIf', array('tplRelatedLinks','relatedLinksIf'));
    $core->tpl->addValue('RelatedLinkTitle', array('tplRelatedLinks','relatedLinkTitle'));
    $core->tpl->addValue('RelatedLinkURL', array('tplRelatedLinks','relatedLinkURL'));
    $core->tpl->addValue('RelatedLinkImage', array('tplRelatedLinks','relatedLinkImage'));

    if ($core->blog->settings->relatedlinks->automatic_content) {
        $core->addBehavior('publicEntryAfterContent',array('relatedLinksBehaviors','publicEntryAfterContent'));
    }
}

require dirname(__FILE__).'/_widgets.php';
