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
        $this->loadHelpers();
        $this->createBucket();
        $this->registerScripts();
    }

    protected function loadHelpers()
    {
        $helpers = array(
            dirname(__FILE__) . '/../helpers.php',
        );

        foreach ($helpers as $helper) {
            $helper = realpath($helper);
            if ($helper) {
                require_once $helper;
            }
        }
    }

    protected function createBucket()
    {
        $this->bucket = new Bucket();
    }

    protected function registerScripts()
    {
    }

    public function add
}
