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
use Dotclear\Database\StaticRecord;
use Dotclear\Helper\File\Path;
use Dotclear\Helper\Html\Html;
use Dotclear\App;
use Dotclear\Plugin\widgets\WidgetsElement;

class Template
{
    public static function widget(WidgetsElement $w): string
    {
        if (!My::settings()->active) {
            return '';
        }

        if (App::url()->type !== 'post' || App::frontend()->context()->related_links->isEmpty()) {
            return '';
        }

        $res = '';
        if ($w->title) {
            $res .= '<h2>' . $w->title . '</h2>';
        }
        $res .= '<ul class="related-links-post">';
        while (App::frontend()->context()->related_links->fetch()) {
            $res .= sprintf(
                '<li><a href="%s">%s</a></li>',
                App::blog()->url() . App::postTypes()->getPostPublicURL(
                    'post',
                    Html::sanitizeURL(App::frontend()->context()->related_links->url)
                ),
                App::frontend()->context()->related_links->title
            );
        }
        $res .= '</ul>';

        return $w->renderDiv((bool) $w->content_only, 'related-links-widget ' . $w->class, '', $res);
    }

    public static function relatedLinksIf($attr, string $content): string
    {
        $res = "<?php\n";
        $res .= 'if (Dotclear\Plugin\relatedLinks\My::settings()->active && App::url()->type==\'post\'):';
        $res .= 'App::frontend()->context()->related_links = new Dotclear\Plugin\relatedLinks\RelatedLinks(App::frontend()->context()->posts->post_id);';
        $res .= 'App::frontend()->context()->related_links = App::frontend()->context()->related_links->getList();';
        $res .= 'if (!App::frontend()->context()->related_links->isEmpty()):?>' . $content . '<?php endif;?>';
        $res .= '<?php endif;?>';

        return $res;
    }

    public static function relatedLinks($attr, string $content): string
    {
        return '<?php while (App::frontend()->context()->related_links->fetch()):?>' . $content . '<?php endwhile;?>';
    }

    public static function relatedLinkURL($attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'App::postTypes()->getPostPublicURL(\'post\',Html::sanitizeURL(App::frontend()->context()->related_links->url))') . ';?>';
    }

    public static function relatedLinkTitle($attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);
        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->related_links->title') . '; ?>';
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
    public static function relatedLinkImage($attr): string
    {
        if (!My::settings()->content_with_image) {
            return '';
        }

        $res = "<?php\n";
        $res .= '$params = array();';

        if (!empty($attr['size'])) {
            $res .= "\$params['size'] = '" . Html::escapeHTML($attr['size']) . "';";
        } else {
            $res .= "\$params['size'] = '';";
        }
        if (!empty($attr['class'])) {
            $res .= "\$params['class'] = '" . Html::escapeHTML($attr['class']) . "';";
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

        $res .= ' echo Dotclear\Plugin\relatedLinks\Template::EntryFirstImageHelper(App::frontend()->context()->related_links, $params);';
        $res .= '?>';

        return $res;
    }

	public static function EntryFirstImageHelper(StaticRecord $post, array $params)
	{
	    try {
	        $sizes = implode('|', array_keys(App::media()->thumb_sizes)) . '|o';
	        if (!preg_match('/^' . $sizes . '$/', $params['size'])) {
	            $params['size'] = 's';
	        }
	        $p_url = App::blog()->settings()->system->public_url;
	        $p_site = preg_replace('#^(.+?//.+?)/(.*)$#', '$1', App::blog()->url());
	        $p_root = App::blog()->publicPath();

	        $pattern = '(?:' . preg_quote($p_site, '/') . ')?' . preg_quote($p_url, '/');
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
	                    $src = $p_url . ($dirname != '/' ? $dirname : '') . '/' . $src;
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
	        App::error()->add($e->getMessage());
	    }
	}

	private static function ContentFirstImageLookup(string $root, string $img, string $size): string | bool
	{
	    // Get base name and extension
	    $info = Path::info($img);
	    $base = $info['base'];
	    $res = false;

	    try {
	        $sizes = implode('|', array_keys(App::media()->thumb_sizes));
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
	        App::error()->add($e->getMessage());
	    }

	    if ($res) {
	        return $res;
	    }

      return false;
	}
}
