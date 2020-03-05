<?php
namespace Jankx\Asset;

abstract class AssetItem implements AssetInterface
{
    protected $hasDependences = false;
    public $dependences = [];
    public $id;
    public $url = '';
    public $version = null;

    public function __construct($id, $url, $dependences, $version)
    {
        $this->id = $id;
        $this->url = $url;
        $this->dependences = $dependences;
        $this->version = $version;

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
