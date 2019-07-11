<?php
namespace Jankx\Asset;

class Bucket
{
    const JANKX_HEADER_INIT_SCRIPT = 1;
    const JANKX_FOOTER_INIT_SCRIPT = 2;
    const JANKX_FOOTER_EXEC_SCRIPT = 3;

    public $headerScripts = [],
        $headerStyles = [],
        $stylesheets = [],
        $initFooterScripts = [],
        $footerScripts = [],
        $executeFooterScripts = [];

    public $enqueueCSS = [],
        $enqueueJS = [];

    public function css($handler, $cssUrl = null, $dependences = [], $version = null, $media = 'all')
    {
        if (!empty($cssUrl)) {
            $cssItem = new CssItem($handler, $cssUrl, $dependences, $version, $media);
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

    public function js($handler, $jsUrl = null, $dependences = [], $version = null, $isFooterScript = true)
    {
        if (!empty($jsUrl)) {
            $jsItem = new JsItem($handler, $jsUrl, $dependences, $version, $isFooterScript);
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

    public function executeScript($jsContent)
    {
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
