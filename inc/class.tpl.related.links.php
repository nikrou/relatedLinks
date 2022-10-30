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

class tplRelatedLinks
{
    public static function widget($w)
    {
        if (!dcCore::app()->blog->settings->relatedlinks->active) {
            return;
        }

        if (dcCore::app()->url->type != 'post' || dcCore::app()->ctx->related_links->isEmpty()) {
            return;
        }

        $res = '';
        if ($w->title) {
            $res .= '<h2>' . $w->title . '</h2>';
        }
        $res .= '<ul class="related-links-post">';
        while (dcCore::app()->ctx->related_links->fetch()) {
            $res .= sprintf(
                '<li><a href="%s">%s</a></li>',
                dcCore::app()->blog->url . dcCore::app()->getPostPublicURL(
                    'post',
                    html::sanitizeURL(dcCore::app()->ctx->related_links->url)
                ),
                dcCore::app()->ctx->related_links->title
            );
        }
        $res .= '</ul>';

        if (version_compare(dcCore::app()->getVersion(), '2.6', '>=')) {
            return '<div class="related_links">' . $res . '</div>';
        } else {
            return $w->renderDiv($w->content_only, 'related-links-widget ' . $w->class, '', $res);
        }
    }

    public static function relatedLinksIf($attr, $content)
    {
        $res = "<?php\n";
        $res .= 'if (dcCore::app()->blog->settings->relatedlinks->active && dcCore::app()->url->type==\'post\'):';
        $res .= 'dcCore::app()->ctx->related_links = new relatedLinks(dcCore::app()->ctx->posts->post_id);';
        $res .= 'dcCore::app()->ctx->related_links = dcCore::app()->ctx->related_links->getList();';
        $res .= 'if (!dcCore::app()->ctx->related_links->isEmpty()):?>' . $content . '<?php endif;?>';
        $res .= '<?php endif;?>';

        return $res;
    }

    public static function relatedLinks($attr, $content)
    {
        return '<?php while (dcCore::app()->ctx->related_links->fetch()):?>' . $content . '<?php endwhile;?>';
    }

    public static function relatedLinkURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->url.dcCore::app()->getPostPublicURL(\'post\',html::sanitizeURL(dcCore::app()->ctx->related_links->url))') . ';?>';
    }

    public static function relatedLinkTitle($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->related_links->title') . '; ?>';
    }

    /*dtd
      <!ELEMENT tpl:RelatedLinkImage - O -- Extracts entry first image if exists -->
      <!ATTLIST
      size (sq|t|s|m|o) #IMPLIED -- Image size to extract
      class CDATA #IMPLIED -- Class to add on image tag
      no_tag (1|0) #IMPLIED	-- Return image URL without HTML tag (default 0)
      content_only	(1|0) #IMPLIED	-- Search in content entry only, not in excerpt (default 0)
      >
     */
    public static function relatedLinkImage($attr)
    {
        if (!dcCore::app()->blog->settings->relatedlinks->content_with_image) {
            return;
        }

        $f = dcCore::app()->tpl->getFilters($attr);

        $res = "<?php\n";
        $res .= '$params = array();';

        if (!empty($attr['size'])) {
            $res .= "\$params['size'] = '" . html::escapeHTML($attr['size']) . "';";
        } else {
            $res .= "\$params['size'] = '';";
        }
        if (!empty($attr['class'])) {
            $res .= "\$params['class'] = '" . html::escapeHTML($attr['class']) . "';";
        }
        if (!empty($attr['no_tag'])) {
            $res .= "\$params['no_tag'] = 1;";
        } else {
            $res .= "\$params['no_tag'] = 0;";
        }
        if (!empty($attr['content_only'])) {
            $res .= "\$params['content_only'] = 1;";
        } else {
            $res .= "\$params['content_only'] = 0;";
        }
        $res .= ' echo tplRelatedLinks::EntryFirstImageHelper(dcCore::app()->ctx->related_links, $params);';
        $res .= '?>';

        return $res;
    }

	public static function EntryFirstImageHelper(StaticRecord $post, array $params)
	{
	    try {
	        $media = new dcMedia();
	        $sizes = implode('|', array_keys($media->thumb_sizes)) . '|o';
	        if (!preg_match('/^' . $sizes . '$/', $params['size'])) {
	            $params['size'] = 's';
	        }
	        $p_site = preg_replace('#^(.+?//.+?)/(.*)$#', '$1', dcCore::app()->blog->url);
	        $p_root = dcCore::app()->blog->public_path;

	        $pattern = '(?:' . preg_quote($p_site, '/') . ')?' . preg_quote(dcCore::app()->admin->getPageURL(), '/');
	        $pattern = sprintf('/<img.+?src="%s(.*?\.(?:jpg|jpeg|gif|png))"[^>]+/msui', $pattern);

	        $src = '';
	        $alt = '';

	        $subject = $post->post_content_xhtml;
	        if (!$params['content_only']) {
	            $subject = $post->post_excerpt_xhtml . $subject;
	        }
	        if (preg_match_all($pattern, $subject, $m) > 0) {
	            foreach ($m[1] as $i => $img) {
	                if (($src = self::ContentFirstImageLookup($p_root, $img, $params['size'])) !== false) {
	                    $dirname = str_replace('\\', '/', dirname($img));
	                    $src = dcCore::app()->admin->getPageURL() . ($dirname != '/' ? $dirname : '') . '/' . $src;
	                    if (preg_match('/alt="([^"]+)"/', $m[0][$i], $malt)) {
	                        $alt = $malt[1];
	                    }
	                    break;
	                }
	            }
	        }

	        if ($src) {
	            if ($params['no_tag']) {
	                return $src;
	            } else {
	                $res = '<img alt="' . $alt . '" src="' . $src . '"';

	                if (!empty($params['class'])) {
	                    $res .= ' class="' . $params['class'] . '"';
	                }
	                $res .= '/>';

	                return $res;
	            }
	        }
	    } catch (Exception $e) {
	        dcCore::app()->error->add($e->getMessage());
	    }
	}

	private static function ContentFirstImageLookup($root, $img, $size)
	{
	    // Get base name and extension
	    $info = path::info($img);
	    $base = $info['base'];
	    $res = false;

	    try {
	        $media = new dcMedia();
	        $sizes = implode('|', array_keys($media->thumb_sizes));
	        if (preg_match('/^\.(.+)_(' . $sizes . ')$/', $base, $m)) {
	            $base = $m[1];
	        }

	        if ($size != 'o' && file_exists($root . '/' . $info['dirname'] . '/.' . $base . '_' . $size . '.jpg')) {
	            $res = '.' . $base . '_' . $size . '.jpg';
	        } elseif ($size != 'o' && file_exists($root . '/' . $info['dirname'] . '/.' . $base . '_' . $size . '.png')) {
	            $res = '.' . $base . '_' . $size . '.png';
	        } else {
	            $f = $root . '/' . $info['dirname'] . '/' . $base;
	            if (file_exists($f . '.' . $info['extension'])) {
	                $res = $base . '.' . $info['extension'];
	            } elseif (file_exists($f . '.jpg')) {
	                $res = $base . '.jpg';
	            } elseif (file_exists($f . '.jpeg')) {
	                $res = $base . '.jpeg';
	            } elseif (file_exists($f . '.png')) {
	                $res = $base . '.png';
	            } elseif (file_exists($f . '.gif')) {
	                $res = $base . '.gif';
	            } elseif (file_exists($f . '.JPG')) {
	                $res = $base . '.JPG';
	            } elseif (file_exists($f . '.JPEG')) {
	                $res = $base . '.JPEG';
	            } elseif (file_exists($f . '.PNG')) {
	                $res = $base . '.PNG';
	            } elseif (file_exists($f . '.GIF')) {
	                $res = $base . '.GIF';
	            }
	        }
	    } catch (Exception $e) {
	        dcCore::app()->error->add($e->getMessage());
	    }

	    if ($res) {
	        return $res;
	    }

        return false;
	}
}
