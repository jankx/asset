<?php
namespace Jankx\Asset;

use Jankx\Template\Template;
use Jankx\Asset\Engine;

abstract class AssetItem implements AssetInterface
{
    protected $hasDependences = false;
    public $dependences = [];
    public $id;
    public $url = '';
    public $version = null;
    public $preload = false;

    protected static $engine;

    public function __construct($id, $url, $dependences, $version, $preload)
    {
        $this->id = $id;
        $this->url = $url;
        $this->dependences = $dependences;
        $this->version = $version;
        $this->preload = $preload;

        if ($dependences) {
            $this->hasDependences = true;
        }
    }

    public function hasDependences()
    {
        return $this->hasDependences;
    }

    public function getDependences()
    {
        return $this->dependences;
    }

    public static function getEngine()
    {
        if (is_null(self::$engine)) {
            $engine = Engine::create('jankx_asset');

            $engine->setDefaultTemplateDir(sprintf('%s/assets', dirname(JANKX_FRAMEWORK_FILE_LOADER)));
            $engine->setDirectoryInTheme('assets');
            $engine->setupEnvironment();

            do_action_ref_array("jankx_template_engine_{$engine->getName()}_init", array(
                &$engine
            ));

            static::$engine = &$engine;
        }
        return self::$engine;
    }

    public static function loadCustomize($file, $data = array())
    {
        return call_user_func_array(
            array(self::getEngine(), 'render'),
            array($file, $data, false)
        );
    }
}
