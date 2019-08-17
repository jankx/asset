<?php
namespace Jankx\Asset;

use Jankx;
use Jankx\Theme;
use Jankx\Asset\Abstracts\Item as AssetItem;

class Manager
{
    protected static $instance;
    protected $bucket;
    protected $theme;
    protected $mainStylesheet;

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
            )
        ));
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

        $jankxCssDeps = apply_filters('jankx_template_css_dependences', ['fontawesome']);
        $stylesheetName = $this->theme->getInstance()->get_stylesheet();

        if (is_child_theme()) {
            $jankx = $this->theme->getTemplate()->getInstance();
            $stylesheetUri = sprintf('%s/style.css', get_template_directory());
            $jankxCssDeps[] = $jankx;
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
            $this->theme->get('Version')
        );

        $this->mainStylesheet = apply_filters('jankx_main_stylesheet', $stylesheetName);
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
        foreach ($dependences as $handler => $cssItem) {
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

    public function registerScripts()
    {
        $this->callDefaultAssets();

        $this->registerStylesheets(
            $this->bucket->getStylesheets()
        );

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
        foreach ($allscripts as $scripts) {
            foreach ($scripts as $script) {
                $jsScript .= $script;
            }
        }
        $jsScript .= '</script>';
        echo $jsScript;
    }

    public function executeFooterScript()
    {
        $jsScript = '<script>';
        $allscripts = $this->bucket->getInitFooterScripts();
        foreach ($allscripts as $scripts) {
            foreach ($scripts as $script) {
                $jsScript .= $script;
            }
        }
        $jsScript .= '</script>';
        echo $jsScript;
    }
}
