<?php
namespace Jankx\Asset;

use Jankx\Asset\Abstracts\Item;

class CssItem extends Item
{
    protected $isRegistered = false;
    public    $media = true;

    public function __construct($id, $url, $dependences, $version, $media = 'all')
    {
        parent::__construct(
            $id,
            $url,
            $dependences,
            $version
        );
        $this->media = $media;
    }

    public function callDependences($dependences)
    {
    }

    public function call()
    {
        if ($this->isRegistered) {
            wp_enqueue_style($this->id);
        } else {
            // Log error css is not registered
        }
    }

    public function register()
    {
        $this->isRegistered = true;
        return wp_register_style(
            $this->id,
            $this->url,
            $this->dependences,
            $this->version,
            $this->media
        );
    }
}
