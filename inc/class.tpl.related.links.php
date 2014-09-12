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

        $res = '';
        if ($w->title) {
            $res .= '<h2>'.$w->title.'</h2>';
        }
        $res .= '<ul class="related-links-post">';
        while ($_ctx->related_links->fetch()) {
            $res .= sprintf(
                '<li><a href="%s">%s</a></li>',
                $core->blog->url.$core->getPostPublicURL('post',
                html::sanitizeURL($_ctx->related_links->url)
                ),
                $_ctx->related_links->title
            );
        }
        $res .= '</ul>';

		return $w->renderDiv($w->content_only, 'related-links-widget '.$w->class, '', $res);
    }

    public static function relatedLinksIf($attr, $content) {
        $res = "<?php\n";
        $res .= 'if ($core->blog->settings->relatedlinks->active && $core->url->type==\'post\'):';
        $res .= '$_ctx->related_links = new relatedLinks($core, $_ctx->posts->post_id);';
        $res .= '$_ctx->related_links = $_ctx->related_links->getList();';
        $res .= 'if (!$_ctx->related_links->isEmpty()):?>'.$content.'<?php endif;?>';
        $res .= '<?php endif;?>';

        return $res;
    }

    public static function relatedLinks($attr, $content) {
        return '<?php while ($_ctx->related_links->fetch()):?>'.$content.'<?php endwhile;?>';
    }

    public static function relatedLinkURL($attr) {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo '.sprintf($f,'$core->blog->url.$core->getPostPublicURL(\'post\',html::sanitizeURL($_ctx->related_links->url))').';?>';
    }

    public static function relatedLinkTitle($attr) {
        $f = $GLOBALS['core']->tpl->getFilters($attr);
        return '<?php echo '.sprintf($f,'$_ctx->related_links->title').'; ?>';
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
    public static function relatedLinkImage($attr) {
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        $res = "<?php\n";
        $res .= '$params = array();';

		if (!empty($attr['size'])) {
            $res .= "\$params['size'] = '".html::escapeHTML($attr['size'])."';";
        } else {
            $res .= "\$params['size'] = '';";
        }
		if (!empty($attr['class'])) {
            $res .= "\$params['class'] = '".html::escapeHTML($attr['class'])."';";
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
        $res .= ' echo tplRelatedLinks::EntryFirstImageHelper($_ctx->related_links, $params);';
        $res .= '?>';

        return $res;
    }

	public static function EntryFirstImageHelper(StaticRecord $post, array $params) {
		global $core, $_ctx;

		try {
			$media = new dcMedia($core);
			$sizes = implode('|',array_keys($media->thumb_sizes)).'|o';
			if (!preg_match('/^'.$sizes.'$/', $params['size'])) {
				$params['size'] = 's';
			}
			$p_url = $core->blog->settings->system->public_url;
			$p_site = preg_replace('#^(.+?//.+?)/(.*)$#','$1',$core->blog->url);
			$p_root = $core->blog->public_path;

			$pattern = '(?:'.preg_quote($p_site,'/').')?'.preg_quote($p_url,'/');
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
                        $src = $p_url.($dirname != '/' ? $dirname : '').'/'.$src;
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
                    $res = '<img alt="'.$alt.'" src="'.$src.'"';

                    if (!empty($params['class'])) {
                        $res .= ' class="'.$params['class'].'"';
                    }
                    $res .= '/>';

                    return $res;
				}
			}

		} catch (Exception $e) {
			$core->error->add($e->getMessage());
		}
	}

	private static function ContentFirstImageLookup($root, $img, $size) {
		global $core;

		# Get base name and extension
		$info = path::info($img);
		$base = $info['base'];

		try {
			$media = new dcMedia($core);
			$sizes = implode('|',array_keys($media->thumb_sizes));
			if (preg_match('/^\.(.+)_('.$sizes.')$/',$base,$m)) {
				$base = $m[1];
			}

			$res = false;
			if ($size != 'o' && file_exists($root.'/'.$info['dirname'].'/.'.$base.'_'.$size.'.jpg')) {
				$res = '.'.$base.'_'.$size.'.jpg';
			} elseif ($size != 'o' && file_exists($root.'/'.$info['dirname'].'/.'.$base.'_'.$size.'.png')) {
				$res = '.'.$base.'_'.$size.'.png';
			} else {
				$f = $root.'/'.$info['dirname'].'/'.$base;
				if (file_exists($f.'.'.$info['extension'])) {
					$res = $base.'.'.$info['extension'];
				} elseif (file_exists($f.'.jpg')) {
					$res = $base.'.jpg';
				} elseif (file_exists($f.'.jpeg')) {
					$res = $base.'.jpeg';
				} elseif (file_exists($f.'.png')) {
					$res = $base.'.png';
				} elseif (file_exists($f.'.gif')) {
					$res = $base.'.gif';
				} elseif (file_exists($f.'.JPG')) {
					$res = $base.'.JPG';
				} elseif (file_exists($f.'.JPEG')) {
					$res = $base.'.JPEG';
				} elseif (file_exists($f.'.PNG')) {
					$res = $base.'.PNG';
				} elseif (file_exists($f.'.GIF')) {
					$res = $base.'.GIF';
				}
			}
		} catch (Exception $e) {
			$core->error->add($e->getMessage());
		}

		if ($res) {
			return $res;
		}
		return false;
	}
}
