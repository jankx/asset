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
    }

    public function registerScripts()
    {
    }
}
