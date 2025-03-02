<?php
namespace themes\aspect;

if (!defined('DC_RC_PATH')) {
    return;
}

\l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/public');

\dcCore::app()->addBehavior('publicHeadContent', [__NAMESPACE__ . '\tplAspectTheme', 'aspectStylesInline']);
\dcCore::app()->tpl->addValue('aspectLogo', [__NAMESPACE__ . '\tplAspectTheme', 'aspectLogo']);
\dcCore::app()->tpl->addBlock('EntryIfContentIsCut', [__NAMESPACE__ . '\tplAspectTheme', 'EntryIfContentIsCut']);
\dcCore::app()->tpl->addValue('AspectPostScripts', [__NAMESPACE__ . '\tplAspectTheme', 'AspectPostScripts']);
\dcCore::app()->tpl->addValue('AspectSVGIcon', [__NAMESPACE__ . '\tplAspectTheme', 'AspectSVGIcon']);
\dcCore::app()->addBehavior('publicFooterContent', [__NAMESPACE__ . '\tplAspectTheme', 'aspectFooterCredits']);

class tplAspectTheme
{
    public static function aspectStylesInline()
    {
        if (\dcCore::app()->blog->settings->aspect->styles !== null) {
            echo '<style>', \dcCore::app()->blog->settings->aspect->styles, '</style>';
        } else {
            echo '<style>#content-info h2,#site-title,.post-title,dt{font-variant:small-caps;}.post-content > p{margin:1em 0;}</style>';
        }
    }

    /**
     * Displays a logo in the header.
     */
    public static function aspectLogo()
    {
        if (\dcCore::app()->blog->settings->aspect->header_logo_url) {
            $src_image = \dcCore::app()->blog->settings->aspect->header_logo_url ? \html::escapeURL(\dcCore::app()->blog->settings->aspect->header_logo_url) : '';
            $srcset    = '';

            $url_public_relative = \dcCore::app()->blog->settings->system->public_url;
            $public_path         = \dcCore::app()->blog->public_path;

            if ($src_image !== '') {
                $image_url_relative = substr($src_image, strpos($src_image, $url_public_relative));

                $image_path = $public_path . str_replace($url_public_relative . '/', '/', $image_url_relative);

                if (\dcCore::app()->blog->settings->aspect->header_logo_url_2x && \dcCore::app()->blog->settings->aspect->header_logo_url_2x !== $src_image) {
                    $src_image_2x = \html::escapeURL(\dcCore::app()->blog->settings->aspect->header_logo_url_2x);
                } else {
                    $src_image_2x = '';
                }

                if ($src_image_2x !== '') {
                    $srcset = ' srcset="' . $src_image . ' 1x, ' . $src_image_2x . ' 2x"';
                }

                if (getimagesize($image_path)) {
                    $image_width  = (int) getimagesize($image_path)[0];
                    $image_height = (int) getimagesize($image_path)[1];
                } else {
                    $image_width  = 0;
                    $image_height = 0;
                }

                $image_size_attr = '';

                if ($image_width > 0 && $image_height > 0) {
                    if ($image_width <= 120) {
                        $image_size_attr .= ' width="' . $image_width . '" height="' . $image_height . '"';
                    } else {
                        $image_size_attr .= ' width="120" height="' . intval($image_height * (120 / $image_width)) . '"';

                        if ($src_image_2x !== '') {
                            $image_size_attr .= ' sizes="100vw"';
                        }
                    }
                }
            }

            return '<div id=site-logo><a class="site-logo-link" href="' . \html::escapeURL(\dcCore::app()->blog->url) . '"><img alt="' . __('logo-img-alt') . '" class=site-logo itemprop="logo" src=' . $src_image . $srcset . $image_size_attr . '></a></div>';
        }
    }

    // À partir du thème Ductile - http://ductile.dotaddict.org
    public static function EntryIfContentIsCut($attr,$content)
    {
        if (empty($attr['cut_string']) || !empty($attr['full'])) {
            return '';
        }

        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        $short = \dcCore::app()->tpl->getFilters($attr);
        $cut = $attr['cut_string'];
        $attr['cut_string'] = 0;
        $full = \dcCore::app()->tpl->getFilters($attr);
        $attr['cut_string'] = $cut;

        return '<?php if (strlen(' . sprintf($full, '$_ctx->posts->getContent(' . $urls . ')') . ') > ' .
            'strlen(' . sprintf($short, '$_ctx->posts->getContent(' . $urls . ')') . ')) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    public static function AspectPostScripts()
    {
        $qmark = \dcCore::app()->blog->getQmarkURL();
        if (\dcCore::app()->url->type == 'post' || \dcCore::app()->url->type == 'pages') {
            return '<script type="text/javascript">var post_remember_str = \'' . __('Remember me') . '\'</script>' . "\n" . '<script type="text/javascript" src="' . $qmark . 'pf=post.js"></script>';
        }
    }

    public static function AspectSVGIcon($attr)
    {
        $path = '';

        if ( isset( $attr['path'] ) && !empty( $attr['path'] ) ) {
            $path = str_replace( '\'', '"', $attr['path'] );
        }

        $class = 'feather';

        if ( isset( $attr['class'] ) && !empty( $attr['class'] ) ) {
            $class = $attr['class'];
        }

        $viewbox = '0 0 24 24';

        if ( isset( $attr['viewbox'] ) && !empty( $attr['viewbox'] ) ) {
            $viewbox = $attr['viewbox'];
        }

        return '<svg class="' . $class . '" xmlns="http://www.w3.org/2000/svg" viewBox="' . $viewbox . '">' . $path . '</svg>';
    }

    public static function aspectFooterCredits()
    {
        if (\dcCore::app()->blog->settings->aspect->footer_credits !== false) {
            echo '<div class="footer-div" id="copyright"><em>', \dcCore::app()->blog->name, '</em> ', __('is powered by <a href="https://dotclear.org/" target="_blank">Dotclear</a> and <a href="http://themes.dotaddict.org/galerie-dc2/details/aspect" target="_blank">Aspect</a>'), '</div>';
        }
    }
}
