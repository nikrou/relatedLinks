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

class relatedLinksBehaviors
{
    public static function adminPostHeaders()
    {
        $plugin_root = html::stripHostURL(dcCore::app()->blog->getQmarkURL() . 'pf=relatedLinks');
        $res = '<script>';
        $res .= 'var rl_text_confirm_remove = \'' . __('Are you sure you want to remove this post?') . '\';';
        $res .= 'var rl_text_confirm_remove_all = \'' . __('Are you sure you want to remove all posts?') . '\';';
        $res .= '</script>';
        $res .= '<script src="js/jquery/jquery-ui.custom.js"></script>';
        $res .= sprintf('<script src="%s"></script>', $plugin_root . '/js/admin_post_form.js');
        $res .= sprintf('<link rel="stylesheet" media="screen" type="text/css" href="%s"/>', $plugin_root . '/css/related_link.css');

        return $res;
    }

    public static function adminPostForm($post)
    {
        $related_links = null;
        $related_links_ids = '';
        $add_post = '';

        if ($post != null) {
            $manager = new relatedLinks($post->post_id);
            $related_links = $manager->getList();
            $ids = [];
            while ($related_links->fetch()) {
                $ids[] = $related_links->link;
            }
            $related_links->moveStart();
            $related_links_ids = implode('|', $ids);
        } else {
            if (!empty($_POST['related_links_ids'])) {
                $links = explode('|', $_POST['related_links_ids']);
                $manager = new relatedLinks($post);
                $related_links = $manager->getList($links);
                $related_links_ids = $_POST['related_links_ids'];
            }
        }

        include(__DIR__ . '/../tpl/admin_post_form.tpl');
    }

    public static function setRelatedLinks($cur, $post_id)
    {
        $post_id = (integer) $post_id;

        if (!empty($_POST['related_links_ids'])) {
            $links = explode('|', $_POST['related_links_ids']);
            $related_links = new relatedLinks($post_id);
            $related_links->add($links, $_POST['related_link_rank']);
        }
    }

    public static function publicEntryAfterContent()
    {
        if (dcCore::app()->url->type == 'default' || dcCore::app()->url->type == 'default-page') {
            return;
        }

        if (dcCore::app()->blog->settings->relatedlinks->content_with_image) {
            $tpl = 'inc_related_links_with_images.html';
        } else {
            $tpl = 'inc_related_links.html';
        }

        $tplset = dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(__DIR__ . '/../default-templates/' . $tplset)) {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/../default-templates/' . $tplset);
        } else {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/../default-templates/' . DC_DEFAULT_TPLSET);
        }
        $tpl_file = dcCore::app()->tpl->getFilePath($tpl);

        if (!$tpl_file) {
            throw new Exception('Unable to find template ');
        }
        dcCore::app()->ctx->current_tpl = $tpl;

        echo dcCore::app()->tpl->getData(dcCore::app()->ctx->current_tpl);
    }
}
