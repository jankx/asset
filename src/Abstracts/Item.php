<?php
namespace Jankx\Asset\Abstracts;

use Jankx\Asset\Interfaces\ItemInterface;

abstract class Item implements ItemInterface
{
    public $id;
    public $url = '';
    public $dependences = [];
    public $version = null;

    public function __construct($id, $url, $dependences, $version)
    {
        $this->id = $id;
        $this->url = $url;
        $this->dependences = $dependences;
        $this->version = $version;
    }
}
