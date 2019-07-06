<?php
namespace Jankx\Asset;

class Bucket
{
    const JANKX_HEADER_INIT_SCRIPT = 1;
    const JANKX_FOOTER_INIT_SCRIPT = 2;
    const JANKX_FOOTER_EXEC_SCRIPT = 3;

    protected $defaultThumbnail = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEgMSkiPg0KCTxnPg0KCQk8Zz4NCgkJCTxwYXRoIGQ9Ik0yNTUtMUMxMTQuMi0xLTEsMTE0LjItMSwyNTVzMTE1LjIsMjU2LDI1NiwyNTZzMjU2LTExNS4yLDI1Ni0yNTZTMzk1LjgtMSwyNTUtMXogTTI1NSwxNi4wNjcNCgkJCQljNjMuMDU0LDAsMTIwLjU5OCwyNC43NjQsMTYzLjQxMyw2NS4wMzNsLTY1LjMzNiw2NC44MDJMMzM0LjM2LDk3Ljk4N2MtMC44NTMtMi41Ni00LjI2Ny01LjEyLTcuNjgtNS4xMkgxODUuMDI3DQoJCQkJYy0zLjQxMywwLTUuOTczLDEuNzA3LTcuNjgsNS4xMkwxNTYuMDEzLDE1Mi42aC00OC42NGMtMTcuMDY3LDAtMzAuNzIsMTMuNjUzLTMwLjcyLDMwLjcydjE2OC45Ng0KCQkJCWMwLDE3LjA2NywxMy42NTMsMzAuNzIsMzAuNzIsMzAuNzJoNi42NTNsLTM0LjI2LDMzLjk4MUM0MC4yODUsMzc0LjMxOSwxNi4wNjcsMzE3LjM1NCwxNi4wNjcsMjU1DQoJCQkJQzE2LjA2NywxMjMuNTg3LDEyMy41ODcsMTYuMDY3LDI1NSwxNi4wNjd6IE0zMTQuNzMzLDI1NWMwLDMzLjI4LTI2LjQ1Myw1OS43MzMtNTkuNzMzLDU5LjczMw0KCQkJCWMtMTMuNTYzLDAtMjUuOTktNC4zOTYtMzUuOTU3LTExLjg1NGw4NC4xMjUtODMuNDM4QzMxMC40NDksMjI5LjM0LDMxNC43MzMsMjQxLjYxNiwzMTQuNzMzLDI1NXogTTE5NS4yNjcsMjU1DQoJCQkJYzAtMzMuMjgsMjYuNDUzLTU5LjczMyw1OS43MzMtNTkuNzMzYzEzLjY2NSwwLDI2LjE3NCw0LjQ2NywzNi4xNzksMTIuMDI4bC04NC4xODMsODMuNDk1DQoJCQkJQzE5OS42MTMsMjgwLjg1MiwxOTUuMjY3LDI2OC40ODcsMTk1LjI2NywyNTV6IE0zMDMuMzc0LDE5NS4xOTlDMjkwLjIwMSwxODQuNTU4LDI3My4zOTksMTc4LjIsMjU1LDE3OC4yDQoJCQkJYy00Mi42NjcsMC03Ni44LDM0LjEzMy03Ni44LDc2LjhjMCwxOC4xNyw2LjIwNiwzNC43NzksMTYuNjEsNDcuODc3bC02My41NzYsNjMuMDU3SDEwNi41MmMtNy42OCwwLTEzLjY1My01Ljk3My0xMy42NTMtMTMuNjUzDQoJCQkJVjE4My4zMmMwLTcuNjgsNS45NzMtMTMuNjUzLDEzLjY1My0xMy42NTNoNTQuNjEzYzMuNDEzLDAsNi44MjctMi41Niw3LjY4LTUuMTJsMjEuMzMzLTU0LjYxM2gxMjkuNzA3bDE5LjQwNCw0OS42NzUNCgkJCQlMMzAzLjM3NCwxOTUuMTk5eiBNMjA2Ljg0OCwzMTQuOTc0QzIxOS45ODcsMzI1LjUwOSwyMzYuNzAzLDMzMS44LDI1NSwzMzEuOGM0Mi42NjcsMCw3Ni44LTM0LjEzMyw3Ni44LTc2LjgNCgkJCQljMC0xOC4wNjgtNi4xMzgtMzQuNTkyLTE2LjQzNi00Ny42NTVsMzcuOTg4LTM3LjY3OGg0OS4yNzRjNy42OCwwLDEzLjY1Myw1Ljk3MywxMy42NTMsMTMuNjUzdjE2OC45Ng0KCQkJCWMwLDcuNjgtNS45NzMsMTMuNjUzLTEzLjY1MywxMy42NTNIMTU1LjQ2OUwyMDYuODQ4LDMxNC45NzR6IE0yNTUsNDkzLjkzM2MtNjIuOTU0LDAtMTIwLjQxNS0yNC42ODYtMTYzLjIwOC02NC44NDNMMTM4LjI2MiwzODMNCgkJCQlINDAzLjQ4YzE3LjA2NywwLDMwLjcyLTEzLjY1MywzMS41NzMtMzAuNzJWMTgzLjMyYzAtMTcuMDY3LTEzLjY1My0zMC43Mi0zMC43Mi0zMC43MkgzNzAuNTZsNTkuODY1LTU5LjM3Ng0KCQkJCWMzOS4zNjgsNDIuNjM5LDYzLjUwOSw5OS41MjEsNjMuNTA5LDE2MS43NzZDNDkzLjkzMywzODYuNDEzLDM4Ni40MTMsNDkzLjkzMywyNTUsNDkzLjkzM3oiLz4NCgkJCTxwYXRoIGQ9Ik0zODMsMTg2LjczM2MtOS4zODcsMC0xNy4wNjcsNy42OC0xNy4wNjcsMTcuMDY3YzAsOS4zODcsNy42OCwxNy4wNjcsMTcuMDY3LDE3LjA2N3MxNy4wNjctNy42OCwxNy4wNjctMTcuMDY3DQoJCQkJQzQwMC4wNjcsMTk0LjQxMywzOTIuMzg3LDE4Ni43MzMsMzgzLDE4Ni43MzN6Ii8+DQoJCTwvZz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg==';

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
            $jsItem = new JsItem($handler, $jsUrl, $dependences, $version, $initFooterScripts);
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
