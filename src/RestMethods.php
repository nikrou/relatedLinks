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
class RestMethods
{
    public static function addRelatedLink(array $get): array
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new RelatedLinks((int) $get['postId']);
        $manager->addLink((int)$get['linkId']);

        return ['message' => true];
    }

    public static function removeRelatedLink(array $get): array
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        if (empty($get['linkId'])) {
            throw new Exception('No link ID');
        }

        $manager = new RelatedLinks((int) $get['postId']);
        $manager->removeLink((int)$get['linkId']);

        return ['message' => true];
    }

    public static function removeRelatedLinks(array $get): array
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $manager = new RelatedLinks((int) $get['postId']);
        $manager->removeLinks();

        return ['message' => true];
    }

    public static function getRelatedLinks(array $get): array
    {
        if (empty($get['postId'])) {
            throw new Exception('No post ID');
        }

        $manager = new RelatedLinks((int) $get['postId']);
        $rs = $manager->getList();

        $links = [];
        if (!$rs->isEmpty()) {
            while ($rs->fetch()) {
                $links[] = $rs->title;
            }
        }

        return ['links' => $links];
    }
}
