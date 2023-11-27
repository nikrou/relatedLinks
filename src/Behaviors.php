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

use Exception;
use Dotclear\Core\Backend\Page;
use Dotclear\App;
use Dotclear\Database\Cursor;
use form;

class Behaviors
{
    public static function adminPostHeaders(): string
    {
        $res = '<script>';
        $res .= 'var rl_text_confirm_remove = \'' . __('Are you sure you want to remove this post?') . '\';';
        $res .= 'var rl_text_confirm_remove_all = \'' . __('Are you sure you want to remove all posts?') . '\';';
        $res .= '</script>';
        $res .= Page::jsLoad('js/jquery/jquery-ui.custom.js');
        $res .= My::jsLoad('admin_post_form.js');
        $res .= My::cssLoad('related_links.css');

        return $res;
    }

    public static function adminPopupPosts(): string
    {
        return My::jsLoad('popup.js');
    }

    public static function adminPostForm($post): void
    {
        $related_links = null;
        $related_links_ids = '';

        if ($post !== null) {
            $manager = new RelatedLinks((int) $post->getPostId());
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
                $manager = new RelatedLinks($post);
                $related_links = $manager->getList($links);
                $related_links_ids = $_POST['related_links_ids'];
            }
        }

        echo '<div class="area related-links-area clearfix">';
        echo '<label class="bold">', __('Related Links'), '</label>';

        echo '<p>';
        echo '<button type="button" id="add-post">', __('Add new post to list'), '</button>';
        echo '<span>&nbsp;-&nbsp;<button type="button" id="remove-all-posts">', __('Remove all posts?'), '</button></span>';
        echo form::hidden('related_links_ids', $related_links_ids);
        echo '</p>';

        echo '<ul id="related-links-list">';
        if (!empty($related_links) && !$related_links->isEmpty()) {
            while ($related_links->fetch()) {
                echo '<li class="link">';
                echo '<input type="hidden" name="related_link_rank[', $related_links->link, ']" value="', $related_links->position, '"/>';
                echo '<a class="post-', $related_links->link, '" href="', $related_links->url, '">', $related_links->title, '</a>';
                echo '&nbsp;<a class="remove" href="#">[x]</a></li>';
            }
        } else {
            echo '<li id="no-links">';
            echo __('No related link yet');
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    public static function setRelatedLinks(Cursor $cur, int $post_id): void
    {
        $post_id = (integer) $post_id;

        if (!empty($_POST['related_links_ids'])) {
            $links = explode('|', $_POST['related_links_ids']);
            $related_links = new RelatedLinks($post_id);
            $related_links->add($links, $_POST['related_link_rank']);
        }
    }

    public static function publicEntryAfterContent(): void
    {
        if (App::url()->type === 'default' || App::url()->type === 'default-page') {
            return;
        }

        if (My::settings()->content_with_image) {
            $tpl = 'inc_related_links_with_images.html';
        } else {
            $tpl = 'inc_related_links.html';
        }

        $tplset = App::themes()->moduleInfo(App::blog()->settings()->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(__DIR__ . '/../default-templates/' . $tplset)) {
            App::frontend()->template()->setPath(App::frontend()->template()->getPath(), __DIR__ . '/../default-templates/' . $tplset);
        } else {
            App::frontend()->template()->setPath(App::frontend()->template()->getPath(), __DIR__ . '/../default-templates/' . DC_DEFAULT_TPLSET);
        }
        $tpl_file = App::frontend()->template()->getFilePath($tpl);

        if (!$tpl_file) {
            throw new Exception('Unable to find template ');
        }
        App::frontend()->context()->current_tpl = $tpl;

        echo App::frontend()->template()->getData(App::frontend()->context()->current_tpl);
    }
}
