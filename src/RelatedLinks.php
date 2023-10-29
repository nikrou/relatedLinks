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

use dbLayer;
use dcBlog;
use dcCore;
use staticRecord;

class RelatedLinks
{
    private dcBlog $blog;
    private dbLayer $con;
    private string $table;
    private string $table_post;

    public function __construct(private ?int $post_id = null)
    {
        $this->blog = dcCore::app()->blog;
        $this->con = $this->blog->con;
        $this->post_id = $post_id;

        $this->table = $this->blog->prefix . 'related_link';
        $this->table_post = $this->blog->prefix . 'post';
    }

    public function add(array $links, array $positions): void
    {
        $cur = $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM ' . $this->table;
        $strReq .= ' WHERE post_id=' . $this->post_id;
        $rs = $this->con->execute($strReq);

        foreach ($links as $link) {
            $cur->blog_id = (string) $this->blog->id;
            $cur->post_id = (int) $this->post_id;

            $strReq = 'SELECT MAX(id) FROM ' . $this->table;
            $rs = $this->con->select($strReq);
            $cur->id = (integer) $rs->f(0) + 1;

            $cur->position = $positions[$link];

            $cur->link = $link;
            $cur->insert();
        }
        $this->blog->triggerBlog();
    }

    public function removeLink(int $link_id): void
    {
        $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM ' . $this->table;
        $strReq .= ' WHERE post_id=' . $this->post_id;
        $strReq .= ' AND link=' . $link_id;

        $this->con->execute($strReq);
        $this->blog->triggerBlog();
    }

    public function removeLinks(): void
    {
        $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM ' . $this->table;
        $strReq .= ' WHERE post_id=' . $this->post_id;

        $this->con->execute($strReq);
        $this->blog->triggerBlog();
    }

    public function addLink(int $link_id): void
    {
        $cur = $this->con->openCursor($this->table);
        $cur->blog_id = (string) $this->blog->id;
        $cur->post_id = (int) $this->post_id;

        $strReq = 'SELECT MAX(id) FROM ' . $this->table;
        $rs = $this->con->select($strReq);
        $cur->id = (integer) $rs->f(0) + 1;

        $strReq = 'SELECT MAX(position) FROM ' . $this->table;
        $rs = $this->con->select($strReq);
        $cur->position = (integer) $rs->f(0) + 1;

        $cur->link = $link_id;
        $cur->insert();

        $this->blog->triggerBlog();
    }

    public function getList(array $ids = []): staticRecord
    {
        $strReq = 'SELECT link, position, post_title AS title, post_url as url';
        $strReq .= ',post_excerpt_xhtml, post_content_xhtml';
        $strReq .= ' FROM ' . $this->table . ' AS R';
        $strReq .= ' LEFT JOIN ' . $this->table_post . ' AS P';
        $strReq .= ' ON P.post_id=R.link';
        $strReq .= ' WHERE R.blog_id = \'' . $this->con->escape($this->blog->id) . '\'';
        if (count($ids) > 0) {
            $strReq .= ' AND P.post_id in (' . implode(',', $ids) . ')';
        } else {
            $strReq .= ' AND R.post_id=' . $this->post_id;
        }
        $strReq .= ' ORDER BY position asc';

        $rs = $this->con->select($strReq);
        $rs = $rs->toStatic();

        return $rs;
    }

    public function getAllLinks(): staticRecord
    {
        $strReq = 'SELECT Q.post_title as post_title, Q.post_id as post_id, count(link) as nb_links';
        $strReq .= ' FROM ' . $this->table . ' AS R';
        $strReq .= ' LEFT JOIN ' . $this->table_post . ' AS Q';
        $strReq .= ' ON Q.post_id=R.post_id';
        $strReq .= ' WHERE R.blog_id = \'' . $this->con->escape($this->blog->id) . '\'';
        $strReq .= ' GROUP BY R.post_id, post_title, Q.post_id';

        $rs = $this->con->select($strReq);
        $rs = $rs->toStatic();

        return $rs;
    }
}
