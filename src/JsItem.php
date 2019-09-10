<?php
namespace Jankx\Asset;

use Jankx\Asset\Abstracts\Item;

class JsItem extends Item
{
    protected $isRegistered = false;
    public $isFooterScript = true;

    public function __construct($id, $url, $dependences, $version, $isFooterScript = true)
    {
        parent::__construct(
            $id,
            $url,
            $dependences,
            $version
        );
        $this->isFooterScript = $isFooterScript;
    }

    public function call()
    {
        if ($this->isRegistered) {
            wp_enqueue_script($this->id);
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
        return wp_register_script(
            $this->id,
            $this->url,
            $this->dependences,
            $this->version,
            $this->isFooterScript
        );
    }
}
