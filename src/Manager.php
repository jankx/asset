<?php
namespace Jankx\Asset;

use Jankx;
use Jankx\Theme;

class Manager
{
    protected static $instance;
    protected $bucket;
    protected $theme;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->theme = Theme::instance();

        $this->createBucket();
        $this->initHooks();
    }

    protected function createBucket()
    {
        /**
         * Create bucket property for Asset manager
         */
        $this->bucket = new Bucket();

        /**
         * Create asset bucket global variable
         */
        $GLOBALS['asset_bucket'] = $this->bucket;
    }

    protected function initHooks()
    {
        add_action('init', array($this, 'registerDefaultAssets'));
        add_action('jankx_setup_environment', array($this, 'setupAssetManager'));
        add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
        add_action('wp_head', array($this, 'registerHeaderStyles'));
        add_action('wp_head', array($this, 'registerHeaderScripts'));
        add_action('wp_footer', array($this, 'initFooterScripts'), 5);
        add_action('wp_footer', array($this, 'executeFooterScript'), 55);
    }

    public function registerDefaultAssets()
    {
        /**
         * Register default CSS resource to Jankx Asset Manager
         */
        $defaultAssetCSS = apply_filters('jankx_default_css_resources', array());
        foreach ($defaultAssetCSS as $handler => $asset) {
            $asset = wp_parse_args($asset, array(
                'url' => '',
                'dependences' => [],
                'version' => null,
                'media' => true,
            ));

            css($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['media']);
        }
        /**
         * Register default JS resources to Jankx Asset Manager
         */
        $defaultAssetJs = apply_filters('jankx_default_js_resources', array(
            'modernizr' => array(
                'url' => jankx_core_asset_url('vendor/modernizr-3.7.1.min.js'),
                'version' => '3.7.1',
            )
        ));
        foreach ($defaultAssetJs as $handler => $asset) {
            $asset = wp_parse_args($asset, array(
                'url' => '',
                'dependences' => [],
                'version' => null,
                'footer' => true,
            ));

            js($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['footer']);
        }

        /**
         * Unset the life default assets after register to Jankx Asset Manager
         */
        unset($defaultAssetCSS, $defaultAssetJs, $handler, $asset);

        $jankxCssDependences = apply_filters('jankx_template_css_dependences', []);

        if (is_child_theme()) {
        } else {
            $theme = $this->theme->getInstance();
            css(
                $theme->get_stylesheet(),
                get_stylesheet_uri(),
                $jankxCssDependences,
                $theme->get('Version'),
            );
        }

        /**
         * Added no post thumbnail CSS
         */
        style(
            sprintf(
                '.no-thumbnail span {background-image: url("%s");}',
                Jankx::defaultThumbnail()
            )
        );
    }

    public function setupAssetManager($jankx)
    {
        $jankx->defaultThumbnail = function() {
            return apply_filters(
                'jankx_base64_default_thumbnail',
                sprintf('%s/assets/resources/img/noimage.svg', Jankx::vendorUrl())
            );
        };
    }

    protected function callDefaultAssets()
    {
        $theme = $this->theme->getInstance();
        css($theme->get_stylesheet());
    }

    public function registerScripts()
    {
        $this->callDefaultAssets();

        foreach($this->bucket->getStylesheets() as $handler => $cssItem) {
            $cssItem->register();
        }

        foreach ($this->bucket->getEnqueueCss() as $handler) {
            if ($this->bucket->isRegistered($handler, true)) {
                $asset = $this->bucket->getStylesheet($handler);
                $asset->call();
            } else {
                wp_enqueue_style($handler);
            }
        }
    }

    public function registerHeaderStyles()
    {
        $css = '<style>';
        $allStyles = $this->bucket->getStyles();
        foreach ($allStyles as $media => $styles) {
            if ($media === 'all') {
                foreach ($styles as $style) {
                    $css .= $style;
                }
            } else {
                foreach ($styles as $style) {
                    $css .= sprintf('@media %1%s {
                        %2%ss
                    }', $media, $style);
                }
            }
        }
        $css .= '</style>';
        echo $css;
    }

    public function registerHeaderScripts()
    {
    }

    public function initFooterScripts()
    {
    }

    public function executeFooterScript()
    {
    }


}
