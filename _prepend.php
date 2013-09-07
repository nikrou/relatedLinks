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

if (!defined('DC_RC_PATH')) { return; }

$__autoload['relatedLinksBehaviors'] = dirname(__FILE__).'/inc/class.related.links.behaviors.php';
$__autoload['relatedLinks'] = dirname(__FILE__).'/inc/class.related.links.php';
$__autoload['tplRelatedLinks'] = dirname(__FILE__).'/inc/class.tpl.related.links.php';
$__autoload['relatedLinksRestMethods'] = dirname(__FILE__).'/inc/class.related.links.rest.methods.php';

$core->rest->addFunction('addRelatedLink',array('relatedLinksRestMethods','addRelatedLink'));
$core->rest->addFunction('removeRelatedLink',array('relatedLinksRestMethods','removeRelatedLink'));
$core->rest->addFunction('removeRelatedLinks',array('relatedLinksRestMethods','removeRelatedLinks'));
$core->rest->addFunction('getRelatedLinks',array('relatedLinksRestMethods','getRelatedLinks'));
