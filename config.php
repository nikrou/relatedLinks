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

$default_tab = 'relatedlinks_settings';

dcCore::app()->blog->settings->addNameSpace('relatedlinks');
$relatedlinks_active = dcCore::app()->blog->settings->relatedlinks->active;
$relatedlinks_was_actived = $relatedlinks_active;
$relatedlinks_automatic_content = dcCore::app()->blog->settings->relatedlinks->automatic_content;
$relatedlinks_content_with_image = dcCore::app()->blog->settings->relatedlinks->content_with_image;

if (!empty($_POST['saveconfig'])) {
    try {
        $relatedlinks_active = (empty($_POST['relatedlinks_active']))?false:true;
        dcCore::app()->blog->settings->relatedlinks->put('active', $relatedlinks_active, 'boolean');

        // change other settings only if they were in html page
        if ($relatedlinks_was_actived) {
            $relatedlinks_automatic_content = (empty($_POST['relatedlinks_automatic_content']))?false:true;
            dcCore::app()->blog->settings->relatedlinks->put('automatic_content', $relatedlinks_automatic_content, 'boolean');

            $relatedlinks_content_with_image = (empty($_POST['relatedlinks_content_with_image']))?false:true;
            dcCore::app()->blog->settings->relatedlinks->put('content_with_image', $relatedlinks_content_with_image, 'boolean');
        }

        $message = __('The configuration has been updated.');
    } catch(Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
} elseif (!empty($_POST['related_links_list'])) {
    foreach ($_POST['related_links_list'] as $post_id) {
        $manager = new relatedLinks($post_id);
        $manager->removeLinks();
    }
    unset($manager);
    $default_tab = 'relatedlinks_links';

    $message = __('Related links deleted');
}

$manager = new relatedLinks(null);
$related_links = $manager->getAllLinks();

$combo_action[__('Delete')] = [__('Delete') => 'delete'];

include(__DIR__ . '/tpl/index.tpl');
