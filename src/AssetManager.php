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
            add_action('wp_enqueue_scripts', array($this, 'callScripts'), 55);

            add_action('wp_head', array($this, 'registerHeaderStyles'), 30);
            add_action('wp_head', array($this, 'registerHeaderScripts'), 30);

            add_action('wp_footer', array($this, 'initFooterScripts'), 5);
            add_action('wp_print_footer_scripts', array($this, 'executeFooterScript'), 30);
        }

        public function registerDefaultAssets()
        {
            /**
             * Register default CSS resource to Jankx Asset Manager
             */
            $jankxBaseData = get_file_data(
                sprintf('%s/resources/css/jankx.css', jankx_core_asset_directory()),
                array('version' => 'Version')
            );
            $defaultAssetCSS = apply_filters('jankx_default_css_resources', array(
                'jankx-base' => array(
                    'url' => jankx_core_asset_url('css/jankx.css'),
                    'version' => $jankxBaseData['version'],
                ),
                'hover' => array(
                    'url' => jankx_core_asset_url('lib/hover/css/hover.css'),
                    'version' => '2.3.2',
                ),
                'tether' => array(
                    'url' => jankx_core_asset_url('lib/tether/css/tether.css'),
                    'version' => '1.4.7',
                ),
                'tether-drop' => array(
                    'url' => jankx_core_asset_url('lib/drop/css/drop-theme-basic.css'),
                    'version' => '1.2.2',
                    'dependences' => array('tether')
                ),
                'choices' => array(
                    'url' => jankx_core_asset_url('lib/Choices/styles/base.css'),
                    'version' => '9.0.1',
                ),
                'splide' => array(
                    'url' => jankx_core_asset_url('lib/splide/css/splide-core.min.css'),
                    'version' => '2.4.12',
                ),
            ));
            foreach ($defaultAssetCSS as $handler => $asset) {
                $asset = wp_parse_args($asset, array(
                    'url' => '',
                    'dependences' => array(),
                    'version' => null,
                    'media' => 'all',
                    'preload' => false
                ));

                if (empty($asset['url'])) {
                    continue;
                }

                css($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['media'], $asset['preload']);
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
                    'version' => '1.4.7',
                ),
                'tether-drop' => array(
                    'url' => jankx_core_asset_url('lib/drop/js/drop.js'),
                    'version' => '1.2.2',
                    'dependences' => array('tether')
                ),
                'slideout' => array(
                    'url' => jankx_core_asset_url('lib/slideout/slideout.js'),
                    'version' => '1.0.1',
                ),
                'splide' => array(
                    'url' => jankx_core_asset_url('lib/splide/js/splide.js'),
                    'version' => '2.4.12',
                ),
                'micromodal' => array(
                    'url' => jankx_core_asset_url('lib/micromodal/micromodal.js'),
                    'version' => '0.4.6',
                ),
                'choices' => array(
                    'url' => jankx_core_asset_url('lib/Choices/scripts/choices.js'),
                    'version' => '9.0.1',
                ),
                'fslightbox-basic' => array(
                    'url' => jankx_core_asset_url('lib/fslightbox-basic/fslightbox.js'),
                    'version' => '3.2.3',
                ),
                'sharing' => array(
                    'url' => jankx_core_asset_url('lib/vanilla-sharing/vanilla-sharing.umd.js'),
                    'version' => '6.0.5',
                )
            ));

            foreach ($defaultAssetJs as $handler => $asset) {
                $asset = wp_parse_args($asset, array(
                    'url' => '',
                    'dependences' => array(),
                    'version' => null,
                    'footer' => true,
                    'preload' => false,
                ));

                if (empty($asset['url'])) {
                    continue;
                }

                js($handler, $asset['url'], $asset['dependences'], $asset['version'], $asset['footer'], $asset['preload']);
            }

            /**
             * Unset the life default assets after register to Jankx Asset Manager
             */
            unset($defaultAssetCSS, $defaultAssetJs, $handler, $asset);

            if (current_theme_supports('render_js_template')) {
                $templateJsFunc = file_get_contents(sprintf(
                    '%s/resources/lib/JavaScript-Templates.js',
                    jankx_core_asset_directory()
                ));

                init_script(sprintf(
                    '<script>%s</script>',
                    $templateJsFunc
                ), true);
            }
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
            $jankxCssDeps = array('jankx-base', 'material-icons');
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
                apply_filters('jankx_asset_css_dependences', $jankxCssDeps, $stylesheetName),
                $this->theme->version
            );

            $assetDirectory = sprintf('%s/assets', realpath(dirname(JANKX_FRAMEWORK_FILE_LOADER) . '/../../..'));
            $appJsVer = $this->theme->version;
            $appJsName = '';
            if (file_exists($appjs = sprintf('%s/js/app.js', $assetDirectory))) {
                $appJsName = 'app';
                $abspath = constant('ABSPATH');
                if (PHP_OS === 'WINNT') {
                    $abspath = str_replace('\\', '/', $abspath);
                    $appjs = str_replace('\\', '/', $appjs);
                }
                js(
                    $appJsName,
                    str_replace($abspath, site_url('/'), $appjs),
                    apply_filters('jankx_asset_js_dependences', array()),
                    $appJsVer,
                    true
                );
            }

            $this->mainStylesheet = apply_filters('jankx_main_stylesheet', $stylesheetName, $jankxCssDeps);
            $this->mainJs         = apply_filters('jankx_main_js', $appJsName);
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
                        $css .= sprintf('@media %1$s {
                            %2$s
                        }', $media, $style);
                    }
                }
            }
            $css .= '</style>';
            echo $css;
        }

        public function registerHeaderScripts()
        {
            $allscripts = $this->bucket->getHeaderScripts();
            $jsScript   = '';
            foreach ($allscripts as $script) {
                $jsScript .= $script . PHP_EOL;
            }
            echo $jsScript;
        }

        public function initFooterScripts()
        {
            $allscripts = $this->bucket->getInitFooterScripts();
            $jsScript   = '';
            foreach ($allscripts as $script) {
                $jsScript .= $script . PHP_EOL;
            }
            echo $jsScript;
        }

        public function executeFooterScript()
        {
            $jsScript = '';
            $allscripts = $this->bucket->getExcuteFooterScripts();
            foreach ($allscripts as $script) {
                $jsScript .= $script . PHP_EOL;
            }
            echo $jsScript;
        }
    }
}
