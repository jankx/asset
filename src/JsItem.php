<?php
namespace Jankx\Asset;

class JsItem extends AssetItem
{

    protected $isRegistered = false;
    public $isFooterScript  = true;
    public $preload         = false;

    public function __construct($id, $url, $dependences = array(), $version = null, $isFooterScript = true, $preload = false)
    {
        parent::__construct(
            $id,
            $url,
            $dependences,
            $version,
            $preload
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
