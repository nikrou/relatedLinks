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

class relatedLinksRestMethods
{
    public static function addRelatedLink($core,$get) {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new relatedLinks($core,(int)$get['postId']);
        $manager->addLink((int)$get['linkId']);

        return true;
    }

    public static function removeRelatedLink($core,$get) {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new relatedLinks($core,(int)$get['postId']);
        $manager->removeLink((int)$get['linkId']);

        return true;
    }

    public static function removeRelatedLinks($core,$get) {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $manager = new relatedLinks($core,(int)$get['postId']);
        $manager->removeLinks();

        return true;
    }

    public static function getRelatedLinks($core,$get) {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $response = array();

        $manager = new relatedLinks($core,(int)$get['postId']);
        $rs = $manager->getList();

        if (!$rs->isEmpty()) {
            $rsp = new xmlTag();
            while ($rs->fetch()) {
                $rsp->related_link($rs->title);
            }

            return $rsp;
        } else {
            return false;
        }
    }
}
