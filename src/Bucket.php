<?php
namespace Jankx\Asset;

class Bucket
{
    protected static $instance;

    public $headerScripts = [];
    public $headerStyles = [];
    public $stylesheets = [];
    public $initFooterScripts = [];
    public $footerScripts = [];
    public $executeFooterScripts = [];

    public $enqueueCSS = [];
    public $enqueueJS = [];

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function css($handler, $cssUrl = null, $dependences = [], $version = null, $media = 'all', $preload = false)
    {
        if (!empty($cssUrl)) {
            $cssItem = new CssItem($handler, $cssUrl, $dependences, $version, $media, $preload);
            $this->stylesheets[$handler] = $cssItem;
        } elseif ($this->isRegistered($handler)) {
            $this->enqueueCSS[] = $handler;
        } else {
            // Log CSS error
        }
    }

    public function style($cssContent, $media = 'all')
    {
        $this->headerStyles[$media][] = $cssContent;
    }

    public function js($handler, $jsUrl = null, $dependences = [], $version = null, $isFooterScript = true, $preload = false)
    {
        if (!empty($jsUrl)) {
            $jsItem = new JsItem($handler, $jsUrl, $dependences, $version, $isFooterScript, $preload);
            $this->footerScripts[$handler] = $jsItem;
        } elseif ($this->isRegistered($handler, false)) {
            $this->enqueueJS[] = $handler;
        } else {
            // Log JS error
        }
    }

    public function script($jsContent, $isHeaderScript = false)
    {
        if ($isHeaderScript) {
            $this->headerScripts[] = $jsContent;
        } else {
            $this->initFooterScripts[] = $jsContent;
        }
    }

    public function executeScript($jsContent, $autoWrapByScriptTag = false)
    {
        if ($autoWrapByScriptTag) {
            $jsContent = '<script>' . $jsContent . '</script>';
        }
        $this->executeFooterScripts[] = $jsContent;
    }

    public function getHeaderScripts()
    {
        return $this->headerScripts;
    }

    public function getStyles()
    {
        return $this->headerStyles;
    }

    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    public function getStylesheet($handler)
    {
        if (isset($this->stylesheets[$handler])) {
            return $this->stylesheets[$handler];
        }
    }

    public function getJavascript($handler)
    {
        if (isset($this->footerScripts[$handler])) {
            return $this->footerScripts[$handler];
        }
    }

    public function getInitFooterScripts()
    {
        return $this->initFooterScripts;
    }

    public function getFooterScipts()
    {
        return $this->footerScripts;
    }

    public function getExcuteFooterScripts()
    {
        return $this->executeFooterScripts;
    }

    public function getEnqueueCss()
    {
        return $this->enqueueCSS;
    }

    public function getEnqueueJs()
    {
        return $this->enqueueJS;
    }

    public function isRegistered($handler, $isStylesheet = true)
    {
        /**
         * Get all handler keys
         */
        $handlers = $isStylesheet ?
            array_keys($this->stylesheets) :
            array_keys($this->footerScripts);

        return in_array($handler, $handlers, true);
    }
}
