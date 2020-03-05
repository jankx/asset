<?php
namespace Jankx\Asset;

class CssItem extends AssetItem
{

    protected $isRegistered = false;
    public $media           = 'all';

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
        if ($this->isRegistered) {
            return;
        }

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
