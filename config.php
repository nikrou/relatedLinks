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

if (!defined('DC_CONTEXT_ADMIN')) { exit; }

$default_tab = 'relatedlinks_settings';

$core->blog->settings->addNameSpace('relatedlinks');
$relatedlinks_active = $core->blog->settings->relatedlinks->active;
$relatedlinks_automatic_content = $core->blog->settings->relatedlinks->automatic_content;

if (!empty($_POST['saveconfig'])) {
    try {
        if ($relatedlinks_active) {
            $relatedlinks_automatic_content = (empty($_POST['relatedlinks_automatic_content']))?false:true;
            $core->blog->settings->relatedlinks->put('automatic_content', $relatedlinks_automatic_content, 'boolean');
        }

        $relatedlinks_active = (empty($_POST['relatedlinks_active']))?false:true;
        $core->blog->settings->relatedlinks->put('active', $relatedlinks_active, 'boolean');

        $message = __('Configuration successfully updated.');
    } catch(Exception $e) {
        $core->error->add($e->getMessage());
    }
} elseif (!empty($_POST['related_links_list'])) {
    foreach ($_POST['related_links_list'] as $post_id) {
        $manager = new relatedLinks($core, $post_id);
        $manager->removeLinks();
    }
    unset($manager);
    $default_tab = 'relatedlinks_links';

    $message = __('Related links successfully deleted');
}

$manager = new relatedLinks($core, null);
$related_links = $manager->getAllLinks();

$combo_action[__('Delete')] = array(__('Delete') => 'delete');

include(dirname(__FILE__).'/tpl/index.tpl');
