<?php
namespace Jankx\Asset;

use Jankx;
use Jankx\Theme;
use Jankx\Asset\Abstracts\Item as AssetItem;

class Manager
{
    protected static $instance;
    protected $bucket;
    protected $mainJs;
    protected $mainStylesheet;
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
        $this->bucket = Bucket::instance();

        /**
         * Create asset bucket global variable
         */
        $GLOBALS['asset_bucket'] = $this->bucket;
    }

    protected function initHooks()
    {
        add_action('jankx_setup_environment', array($this, 'setupAssetManager'));
        add_action('wp_enqueue_scripts', array($this, 'registerDefaultAssets'));
        add_action('wp_enqueue_scripts', array($this, 'registerScripts'), 35);
        add_action('wp_enqueue_scripts', array($this, 'callScripts'), 35);

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
        $defaultAssetCSS = apply_filters('jankx_default_css_resources', array(
            'fontawesome' => array(
                'url' => jankx_core_asset_url('vendor/fontawesome/css/all.css'),
                'version' => '5.9.0',
            ),
            'tether' => array(
                'url' => jankx_core_asset_url('vendor/tether/css/tether.css'),
                'version' => '1.3.3',
            ),
            'glide' => array(
                'url' => jankx_core_asset_url('vendor/glide/glide.core.css'),
                'version' => '3.4.1',
            ),
        ));
        foreach ($defaultAssetCSS as $handler => $asset) {
            $asset = wp_parse_args($asset, array(
                'url' => '',
                'dependences' => [],
                'version' => null,
                'media' => true,
            ));

            if (empty($asset['url'])) {
                continue;
            }

            css($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['media']);
        }
        /**
         * Register default JS resources to Jankx Asset Manager
         */
        $defaultAssetJs = apply_filters('jankx_default_js_resources', array(
            'modernizr' => array(
                'url' => jankx_core_asset_url('vendor/modernizr-3.7.1.min.js'),
                'version' => '3.7.1',
            ),
            'tether' => array(
                'url' => jankx_core_asset_url('vendor/tether/js/tether.js'),
                'version' => '1.3.3',
            ),
            'glide' => array(
                'url' => jankx_core_asset_url('vendor/glide/glide.js'),
                'version' => '3.4.1',
            ),
            'micromodal' => array(
                'url' => jankx_core_asset_url('vendor/micromodal/micromodal.js'),
                'version' => '0.4.2',
            ),
        ));

        foreach ($defaultAssetJs as $handler => $asset) {
            $asset = wp_parse_args($asset, array(
                'url' => '',
                'dependences' => [],
                'version' => null,
                'footer' => true,
            ));

            if (empty($asset['url'])) {
                continue;
            }

            js($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['footer']);
        }

        /**
         * Unset the life default assets after register to Jankx Asset Manager
         */
        unset($defaultAssetCSS, $defaultAssetJs, $handler, $asset);

        $jankxCssDeps = apply_filters('jankx_template_css_dependences', ['fontawesome']);
        $stylesheetName = $this->theme->getInstance()->get_stylesheet();

        if (is_child_theme()) {
            $jankx = $this->theme->getTemplate()->getInstance();
            $stylesheetUri = sprintf('%s/style.css', $jankx->get_template_directory_uri());
            $jankxCssDeps[] = $jankx->get_stylesheet();
            css(
                $jankx->get_stylesheet(),
                $stylesheetUri,
                array(),
                $jankx->get('Version')
            );
        }
        css(
            $stylesheetName,
            get_stylesheet_uri(),
            $jankxCssDeps,
            $this->theme->getInstance()->get('Version')
        );


        $this->mainStylesheet = apply_filters('jankx_main_stylesheet', $stylesheetName, $jankxCssDeps);
        $this->mainJs         = apply_filters('jankx_main_js', '');

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
        $jankx->defaultThumbnail = function () {
            return apply_filters(
                'jankx_base64_default_thumbnail',
                sprintf('%s/assets/resources/img/noimage.svg', Jankx::vendorUrl())
            );
        };
    }

    protected function callDefaultAssets()
    {
        css($this->mainStylesheet);

        if (!empty($this->mainJs)) {
            js($this->mainJs);
        }
    }

    public function registerStylesheets($dependences)
    {
        foreach ((array)$dependences as $handler => $cssItem) {
            if (!$cssItem instanceof AssetItem) {
                $cssItem = $this->bucket->getStylesheet($cssItem);

                if (empty($cssItem)) {
                    continue;
                }
            }

            if ($cssItem->hasDependences()) {
                $this->registerStylesheets($cssItem->getDependences());
            }

            $cssItem->register();
        }
    }

    public function registerJavascripts($dependences)
    {
        foreach ((array)$dependences as $handler => $jsItem) {
            if (!$jsItem instanceof AssetItem) {
                $jsItem = $this->bucket->getStylesheet($jsItem);

                if (empty($jsItem)) {
                    continue;
                }
            }

            if ($jsItem->hasDependences()) {
                $this->registerJavascripts($jsItem->getDependences());
            }

            $jsItem->register();
        }
    }

    public function registerScripts()
    {
        $this->callDefaultAssets();
        $this->registerStylesheets(
            $this->bucket->getStylesheets()
        );
        $this->registerJavascripts(
            $this->bucket->getFooterScipts()
        );
    }

    /**
     * Call all Javascripts and CSS are registered
     */
    public function callScripts()
    {
        foreach ($this->bucket->getEnqueueCss() as $handler) {
            if ($this->bucket->isRegistered($handler, true)) {
                $css = $this->bucket->getStylesheet($handler);
                $css->call();
            } else {
                wp_enqueue_style($handler);
            }
        }
        foreach ($this->bucket->getEnqueueJs() as $handler) {
            if ($this->bucket->isRegistered($handler, false)) {
                $js = $this->bucket->getJavascript($handler);
                $js->call();
            } else {
                wp_enqueue_script($handler);
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
        $jsScript = '<script>';
        $allscripts = $this->bucket->getHeaderScripts();
        foreach ($allscripts as $scripts) {
            foreach ($scripts as $script) {
                $jsScript .= $script;
            }
        }
        $jsScript .= '</script>';
        echo $jsScript;
    }

    public function initFooterScripts()
    {
        $jsScript = '<script>';
        $allscripts = $this->bucket->getInitFooterScripts();
        foreach ($allscripts as $script) {
            $jsScript .= $script;
        }
        $jsScript .= '</script>';
        echo $jsScript;
    }

    public function executeFooterScript()
    {
        $jsScript = '<script>';
        $allscripts = $this->bucket->getExcuteFooterScripts();
        foreach ($allscripts as $script) {
            $jsScript .= $script;
        }
        $jsScript .= '</script>';
        echo $jsScript;
    }
}
