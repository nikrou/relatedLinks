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

class relatedLinks
{
    public function __construct($core, $post_id) {
        $this->core = $core;
        $this->blog = $core->blog;
        $this->con = $this->blog->con;
        $this->post_id = $post_id;

        $this->table = $this->blog->prefix . 'related_link';
        $this->table_post = $this->blog->prefix . 'post';
    }

    public function add($links, $positions) {
        $cur = $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM '.$this->table;
        $strReq .= ' WHERE post_id='.$this->post_id;
        $rs = $this->con->execute($strReq);

        foreach ($links as $link) {
            $cur->blog_id = (string) $this->blog->id;
            $cur->post_id = (int) $this->post_id;

            $strReq = 'SELECT MAX(id) FROM '.$this->table;
            $rs = $this->con->select($strReq);
            $cur->id = (integer) $rs->f(0) + 1;

            $cur->position = $positions[$link];

            $cur->link = $link;
            $cur->insert();
        }
        $this->blog->triggerBlog();
    }

    public function removeLink($link_id) {
        $cur = $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM '.$this->table;
        $strReq .= ' WHERE post_id='.$this->post_id;
        $strReq .= ' AND link='.$link_id;

        $rs = $this->con->execute($strReq);
        $this->blog->triggerBlog();
    }

    public function removeLinks() {
        $cur = $this->con->openCursor($this->table);

        $strReq = 'DELETE FROM '.$this->table;
        $strReq .= ' WHERE post_id='.$this->post_id;

        $rs = $this->con->execute($strReq);
        $this->blog->triggerBlog();
    }

    public function addLink($link_id) {
        $cur = $this->con->openCursor($this->table);
        $cur->blog_id = (string) $this->blog->id;
        $cur->post_id = (int) $this->post_id;

        $strReq = 'SELECT MAX(id) FROM '.$this->table;
        $rs = $this->con->select($strReq);
        $cur->id = (integer) $rs->f(0) + 1;

        $strReq = 'SELECT MAX(position) FROM '.$this->table;
        $rs = $this->con->select($strReq);
        $cur->position = (integer) $rs->f(0) + 1;

        $cur->link = $link_id;
        $cur->insert();

        $this->blog->triggerBlog();
    }

    public function getList($ids=array()) {
        $strReq =  'SELECT link, position, post_title AS title, post_url as url';
        $strReq .= ',post_excerpt_xhtml, post_content_xhtml';
        $strReq .= ' FROM '.$this->table.' AS R';
        $strReq .= ' LEFT JOIN '.$this->table_post.' AS P';
        $strReq .= ' ON P.post_id=R.link';
        $strReq .= ' WHERE R.blog_id = \''.$this->con->escape($this->blog->id).'\'';
        if (count($ids)>0) {
            $strReq .= ' AND P.post_id in ('.implode(',', $ids).')';
        } else {
            $strReq .= ' AND R.post_id='.$this->post_id;
        }
        $strReq .= ' ORDER BY position asc';

        $rs = $this->con->select($strReq);
        $rs = $rs->toStatic();

        return $rs;
    }

    public function getAllLinks($count_only=true) {
        $strReq =  'SELECT Q.post_title as post_title, Q.post_id as post_id, count(link) as nb_links';
        $strReq .= ' FROM '.$this->table.' AS R';
        $strReq .= ' LEFT JOIN '.$this->table_post.' AS Q';
        $strReq .= ' ON Q.post_id=R.post_id';
        $strReq .= ' WHERE R.blog_id = \''.$this->con->escape($this->blog->id).'\'';
        $strReq .= ' GROUP BY R.post_id, post_title, Q.post_id';

        $rs = $this->con->select($strReq);
        $rs = $rs->toStatic();

        return $rs;
    }
}
