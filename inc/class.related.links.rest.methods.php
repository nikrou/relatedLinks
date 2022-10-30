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

class relatedLinksRestMethods
{
    public static function addRelatedLink($get)
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new relatedLinks((int)$get['postId']);
        $manager->addLink((int)$get['linkId']);

        return true;
    }

    public static function removeRelatedLink($get)
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new relatedLinks((int)$get['postId']);
        $manager->removeLink((int)$get['linkId']);

        return true;
    }

    public static function removeRelatedLinks($get)
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $manager = new relatedLinks((int)$get['postId']);
        $manager->removeLinks();

        return true;
    }

    public static function getRelatedLinks($get)
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $response = [];

        $manager = new relatedLinks((int)$get['postId']);
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
