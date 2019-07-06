<?php
namespace Jankx\Asset;

use Jankx\Asset\Abstracts\Item;

class CssItem extends Item
{
    public $media = true;

    public function __construct($id, $url, $dependences, $version, $media = 'all') {
        parent::__construct($id, $url, $dependences, $version);
        $this->media = $media;
    }
}
