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

$q = !empty($_GET['q']) ? $_GET['q'] : null;

$page = !empty($_GET['page']) ? (integer) $_GET['page'] : 1;
$nb_per_page = 10;

$params = [];
$params['limit'] = [(($page - 1) * $nb_per_page), $nb_per_page];
$params['no_content'] = true;
$params['post_status'] = 1;
$params['order'] = 'post_dt DESC';

if ($q) {
    $params['search'] = $q;
}

try {
    $posts = dcCore::app()->blog->getPosts($params);
    $counter = dcCore::app()->blog->getPosts($params, true);
    $post_list = new adminPostMiniList($posts, $counter->f(0));
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

include(__DIR__ . '/tpl/popup.tpl');
