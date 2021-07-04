<?php
namespace Jankx\Asset;

use Jankx\Asset\Cache;

if (!class_exists(AssetManager::class)) {
    class AssetManager
    {
        protected static $instance;

        protected $bucket;

        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct()
        {
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

            add_action('wp_enqueue_scripts', array($this, 'registerScripts'), 35);
            add_action('wp_enqueue_scripts', array($this, 'callScripts'), 55);
            add_action('wp_enqueue_scripts', array(Cache::class, 'load'), 65);

            add_action('wp_head', array($this, 'registerHeaderStyles'), 30);
            add_action('wp_head', array($this, 'registerHeaderScripts'), 30);

            add_action('wp_footer', array($this, 'initFooterScripts'), 5);
            add_action('wp_print_footer_scripts', array($this, 'executeFooterScript'), 30);
        }

        public function registerDefaultAssets()
        {
            $defaultAssetCSS = apply_filters('jankx_default_css_resources', array());
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
            $defaultAssetJs = apply_filters('jankx_default_js_resources', array());

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
