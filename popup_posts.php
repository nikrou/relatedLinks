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

$q = !empty($_GET['q']) ? $_GET['q'] : null;

$page = !empty($_GET['page']) ? (integer) $_GET['page'] : 1;
$nb_per_page =  10;

$params = array();
$params['limit'] = array((($page-1)*$nb_per_page),$nb_per_page);
$params['no_content'] = true;
$params['post_status'] = 1;
$params['order'] = 'post_dt DESC';

if ($q) {
    $params['search'] = $q;
}

try {
    $posts = $core->blog->getPosts($params);
    $counter = $core->blog->getPosts($params,true);
    $post_list = new adminPostMiniList($core,$posts,$counter->f(0));
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}

include(dirname(__FILE__).'/tpl/popup.tpl');
