<?php
// +-----------------------------------------------------------------------+
// | related Links  - a plugin for Dotclear                                |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2010-2018 Nicolas Roudaire       https://www.nikrou.net  |
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

class relatedLinksBehaviors
{
	public static function adminPostHeaders() {
		global $core;

		$plugin_root = html::stripHostURL($core->blog->getQmarkURL().'pf=relatedLinks');
		$res = '<script>';
		$res .= 'var rl_text_confirm_remove = \''.__('Are you sure you want to remove this post?').'\';';
		$res .= 'var rl_text_confirm_remove_all = \''.__('Are you sure you want to remove all posts?').'\';';
		$res .= '</script>';
        $res .= '<script src="js/jquery/jquery-ui.custom.js"></script>';
		$res .= sprintf('<script src="%s"></script>', $plugin_root.'/js/admin_post_form.js');
		$res .= sprintf('<link rel="stylesheet" media="screen" type="text/css" href="%s"/>', $plugin_root.'/css/related_link.css');

		return $res;
	}

	public static function adminPostForm($post) {
		global $core;

		$related_links = null;
		$related_links_ids = '';
		$add_post = '';

		if ($post!=null) {
			$manager = new relatedLinks($core, $post->post_id);
			$related_links = $manager->getList();
			$ids = array();
			while ($related_links->fetch()) {
				$ids[] = $related_links->link;
			}
			$related_links->moveStart();
			$related_links_ids = implode('|', $ids);
		} else {
			if (!empty($_POST['related_links_ids'])) {
				$links = explode('|', $_POST['related_links_ids']);
				$manager = new relatedLinks($core, $post);
				$related_links = $manager->getList($links);
				$related_links_ids = $_POST['related_links_ids'];
			}
		}

		include(dirname(__FILE__).'/../tpl/admin_post_form.tpl');
	}

	public static function setRelatedLinks($cur, $post_id) {
		global $core;

		$post_id = (integer) $post_id;

		if (!empty($_POST['related_links_ids'])) {
			$links = explode('|', $_POST['related_links_ids']);
			$related_links = new relatedLinks($core, $post_id);
			$related_links->add($links, $_POST['related_link_rank']);
		}
	}

	public static function publicEntryAfterContent($core, $_ctx) {
		if ($core->url->type == 'default' || $core->url->type == 'default-page') {
			return;
		}

        if ($core->blog->settings->relatedlinks->content_with_image) {
            $tpl = 'inc_related_links_with_images.html';
        } else {
            $tpl = 'inc_related_links.html';
        }

        $tplset = $core->themes->moduleInfo($core->blog->settings->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(dirname(__FILE__).'/../default-templates/'.$tplset)) {
            $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__).'/../default-templates/'.$tplset);
        } else {
            $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__).'/../default-templates/'.DC_DEFAULT_TPLSET);
        }
		$tpl_file = $core->tpl->getFilePath($tpl);

		if (!$tpl_file) {
			throw new Exception('Unable to find template ');
		}
		$_ctx->current_tpl = $tpl;

		echo $core->tpl->getData($_ctx->current_tpl);
	}
}
