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
}
