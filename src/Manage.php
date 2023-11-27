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

use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Html;
use Dotclear\App;
use form;

class Manage extends Process
{
    public static function init(): bool
    {
        if (!self::status(My::checkContext(My::MANAGE))) {
            return false;
        }

        return self::status(true);
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST['related_links_list'])) {
            foreach ($_POST['related_links_list'] as $post_id) {
                $manager = new RelatedLinks((int) $post_id);
                $manager->removeLinks();
            }
            unset($manager);

            Notices::addSuccessNotice(__('Related links deleted'));
        }

        return true;
    }

    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $default_tab = 'relatedlinks_links';

        $combo_action[__('Delete')] = [__('Delete') => 'delete'];

        $related_links = (new RelatedLinks())->getAllLinks();

        Page::openModule(
            __('relatedLinks'),
            Page::jsPageTabs($default_tab) .
            My::jsLoad('related_links_list.js') .
            My::cssLoad('related_links.css')
        );

        echo Page::breadcrumb([Html::escapeHTML(App::blog()->name()) => '',
            '<a href="' . My::manageUrl() . '">' . __('relatedLinks') . '</a>' => ''
        ]);

        echo Notices::getNotices();

        if (My::settings()->active) {
            echo '<div class="multi-part" id="relatedlinks_links" title="',  __('Links'), '">';
            echo '<p>', __('Posts with related links'), '</p>';
            if (!$related_links->isEmpty()) {
                echo '<form action="', My::manageUrl(), '" method="post">';
                echo '<ul id="related-links-expandable">';
                while ($related_links->fetch()) {
                    echo '<li id="', $related_links->post_id, '">';
                    echo '<img class="related-link-expand">';
                    echo form::checkbox('related_links_list[]', $related_links->post_id);
                    echo '<a href="', App::postTypes()->getPostAdminURL('post', $related_links->post_id), '">';
                    echo $related_links->post_title;
                    echo '</a>';
                    echo '&nbsp;(', $related_links->nb_links, ')';
                    echo '</li>';
                }
                echo '</ul>';

                echo '<div class="two-cols">';
                echo '<p class="col checkboxes-helpers"></p>';
                echo '<p class="col right">';
                echo form::combo('action', $combo_action);
                echo App::nonce()->getFormNonce();
                echo '<input type="submit" value="', __('ok'), '"/>';
                echo '</p>';
                echo '</div>';
                echo '</form>';
            } else {
                echo '<p>', __('No related link yet'), '</p>';
            }
            echo '</div>';

            echo '<div class="multi-part" id="relatedlinks_code" title="', __('Installation'), '">';
            echo '<p>',  __('The plugin define new tags for template:'), '</p>';
            echo '<ul>';
            echo '<li><strong>RelatedLinksIf</strong> (', __('block'), ' : ',  __('to only display related links if there are somes.'), '</li>';
            echo '<li><strong>RelatedLinks</strong> (', __('block'), ') : ', __('loop to display related links.'), '</li>';
            echo '<li><strong>RelatedLinkTitle</strong> (', __('value'), ' : ', __('link label'), '</li>';
            echo '<li><strong>RelatedLinkURL</strong> (', __('value'), ') : ', __('link URL'), '</li>';
            echo '</ul>';

            echo '<p>', __('Code example to add to your "post.html" theme template:'), '</p>';
            echo '<pre class="code">';
            echo Html::escapeHTML(file_get_contents(__DIR__ . '/../default-templates/currywurst/inc_related_links.html'));
            echo '</pre>';

            echo '<p>',  __('Code example (links with images) to add to your "post.html" theme template:'), '</p>';
            echo '<pre class="code">';
            echo Html::escapeHTML(file_get_contents(__DIR__ . '/../default-templates/currywurst/inc_related_links_with_images.html'));
            echo '</pre>';

            echo '</div>';
            echo '<div class="multi-part" id="relatedlinks_about" title="', __('About'), '">';
            echo '<p>';
            echo __('If you want more informations on that plugin or have new ideas to develope it, or want to submit a bug or need help (to install or configure it) or for anything else ...'), '</p>';
            echo '<p>';
            printf(
                __('Go to %sthe dedicated page%s in'),
                '<a href="https://www.nikrou.net/pages/relatedLinks">',
                '</a>'
            );
            echo '<a href="https://www.nikrou.net/">Le journal de nikrou</a>';
            echo '</p>';
            echo '<p>',  __('Made by:');
            echo '<a href="https://www.nikrou.net/contact">Nicolas</a> (nikrou)';
            echo '</p>';
            echo '</div>';
        }

        Page::helpBlock(My::id());
        Page::closeModule();
    }
}
