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

class tplRelatedLinks
{
    public static function widget($w) {
        global $core, $_ctx;

        if (!$core->blog->settings->relatedlinks->active) {
            return;
        }

        if ($core->url->type!='post' || $_ctx->related_links->isEmpty()) {
            return;
        }

        $res = '<div class="related-links-widget">';
        if ($w->title) {
            $res .= '<h2>'.$w->title.'</h2>';
        }
        $res .= '<ul class="related-links-post">';
        while ($_ctx->related_links->fetch()) {
            $res .= sprintf('<li><a href="%s">%s</a></li>',
            $core->blog->url.$core->getPostPublicURL('post',
            html::sanitizeURL($_ctx->related_links->url)
            ),
            $_ctx->related_links->title
            );
        }
        $res .= '</ul>';
        $res .= '</div>';

        return $res;
    }

    public static function relatedLinksIf($attr,$content) {
        $res = "<?php\n";
        $res .= 'if ($core->blog->settings->relatedlinks->active && $core->url->type==\'post\'):';
        $res .= '$_ctx->related_links = new relatedLinks($core, $_ctx->posts->post_id);';
        $res .= '$_ctx->related_links = $_ctx->related_links->getList();';
        $res .= 'if (!$_ctx->related_links->isEmpty()):?>'.$content.'<?php endif;?>';
        $res .= '<?php endif;?>';

        return $res;
    }

    public static function relatedLinks($attr, $content) {
        $res = '<?php while ($_ctx->related_links->fetch()) : ?>'.$content.'<?php endwhile;?>';

        return $res;
    }

    public static function relatedLinkURL($attr) {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo '.sprintf($f,'$core->blog->url.$core->getPostPublicURL(\'post\',html::sanitizeURL($_ctx->related_links->url))').'; ?>';
    }

    public static function relatedLinkTitle($attr) {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo '.sprintf($f,'$_ctx->related_links->title').'; ?>';
    }
}
