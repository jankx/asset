<?php
namespace Jankx\Asset;

if (!class_exists(AssetManager::class)) {
    class AssetManager
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
            $this->theme = wp_get_theme();
            $this->loadHelpers();
            $this->createBucket();
            $this->initHooks();
        }

        public function loadHelpers()
        {
            require_once realpath(dirname(__FILE__) . '/../helpers.php');
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
            add_action('init', array($this, 'registerDefaultAssets'), 5);

            add_action('wp_enqueue_scripts', array($this, 'registerThemeAssets'));

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
                'jankx-base' => array(
                    'url' => jankx_core_asset_url('css/base.css'),
                    'version' => '0.0.11',
                ),
                'fontawesome' => array(
                    'url' => jankx_core_asset_url('lib/fontawesome/css/all.css'),
                    'version' => '5.9.0',
                ),
                'material-icons' => array(
                    'url' => jankx_core_asset_url('lib/MaterialDesign-Webfont/css/materialdesignicons.css'),
                    'version' => '5.5.55',
                ),
                'tether' => array(
                    'url' => jankx_core_asset_url('lib/tether/css/tether.css'),
                    'version' => '1.3.3',
                ),
                'glide' => array(
                    'url' => jankx_core_asset_url('lib/glide/css/glide.core.css'),
                    'version' => '3.4.1',
                ),
                'glide-theme' => array(
                    'url' => jankx_core_asset_url('lib/glide/css/glide.core.css'),
                    'version' => '3.4.1',
                    'dependences' => ['glide']
                ),
                'bulma' => array(
                    'url' => jankx_core_asset_url('lib/bulma/css/bulma.css'),
                    'version' => '0.8.0',
                ),
                'owl-carousel2' => array(
                    'url' => jankx_core_asset_url('lib/OwlCarousel2/assets/owl.carousel.css'),
                    'version' => '2.3.4',
                ),
                'owl-carousel2-default' => array(
                    'url' => jankx_core_asset_url('lib/OwlCarousel2/assets/owl.theme.default.css'),
                    'version' => '2.3.4',
                    'dependences' => ['owl-carousel2']
                ),
                'owl-carousel2-green' => array(
                    'url' => jankx_core_asset_url('lib/OwlCarousel2/assets/owl.theme.green.css'),
                    'version' => '2.3.4',
                    'dependences' => ['owl-carousel2']
                ),
            ));
            foreach ($defaultAssetCSS as $handler => $asset) {
                $asset = wp_parse_args($asset, array(
                    'url' => '',
                    'dependences' => [],
                    'version' => null,
                    'media' => 'all',
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
                    'url' => jankx_core_asset_url('lib/modernizr-3.7.1.min.js'),
                    'version' => '3.7.1',
                ),
                'tether' => array(
                    'url' => jankx_core_asset_url('lib/tether/js/tether.js'),
                    'version' => '1.3.3',
                ),
                'glide' => array(
                    'url' => jankx_core_asset_url('lib/glide/glide.js'),
                    'version' => '3.4.1',
                ),
                'slideout' => array(
                    'url' => jankx_core_asset_url('lib/slideout-1.0.1/slideout.js'),
                    'version' => '1.0.1',
                ),
                'micromodal' => array(
                    'url' => jankx_core_asset_url('lib/micromodal/micromodal.js'),
                    'version' => '0.4.2',
                ),
                'owl-carousel2' => array(
                    'url' => jankx_core_asset_url('lib/OwlCarousel2/owl.carousel.js'),
                    'version' => '2.3.4',
                    'dependences' => ['jquery']
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
        }

        protected function callDefaultAssets()
        {
            css($this->mainStylesheet);

            if (!empty($this->mainJs)) {
                js($this->mainJs);
            }
        }

        public function registerThemeAssets()
        {
            $jankxCssDeps = apply_filters('jankx_template_css_dependences', ['jankx-base', 'fontawesome']);
            $stylesheetName = $this->theme->get_stylesheet();

            if (is_child_theme()) {
                $stylesheetUri = sprintf('%s/style.css', get_template_directory_uri());
                $jankxTemplate = wp_get_theme($this->theme->get_template());
                $jankxCssDeps[] = $jankxTemplate->get_stylesheet();
                css(
                    $jankxTemplate->get_stylesheet(),
                    $stylesheetUri,
                    array(),
                    $jankxTemplate->version
                );
            }

            css(
                $stylesheetName,
                get_stylesheet_uri(),
                $jankxCssDeps,
                $this->theme->version
            );

            $this->mainStylesheet = apply_filters('jankx_main_stylesheet', $stylesheetName, $jankxCssDeps);
            $this->mainJs         = apply_filters('jankx_main_js', '');
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
            $handlers = $this->bucket->getEnqueueCss();
            foreach ($handlers as $handler) {
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
}
