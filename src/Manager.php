<?php
namespace Jankx\Asset;

class Manager
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

    public function __construct()
    {
        $this->createBucket();
        $this->registerDefaultAssets();
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
    }

    public function registerScripts()
    {
        // var_dump($this->bucket);
        // die;
    }

    public function registerHeaderStyles()
    {
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
