<?php
function asset()
{
}

function css($handler, $cssUrl = null, $dependences = [], $version = null, $media = 'all')
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'css'),
        $handler,
        $cssUrl,
        $dependences,
        $version,
        $media
    );
}

function js($handler, $jsUrl = null, $dependences = [], $version = null, $isFooterScript = true)
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'js'),
        $handler,
        $jsUrl,
        $dependences,
        $version,
        $isFooterScript
    );
}

function style($cssContent, $media = 'all')
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'style'),
        $cssContent,
        $media
    );
}

function init_script($js, $isHeaderScript = false)
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'script'),
        $js,
        $isHeaderScript
    );
}

function execute_script($jsContent)
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'executeScript'),
        $jsContent
    );
}

function is_registered_asset($handler, $isStylesheet = true)
{
    return call_user_func(
        array($GLOBALS['asset_bucket'], 'isRegistered'),
        $handler,
        $isStylesheet
    );
}