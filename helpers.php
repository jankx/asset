<?php
function asset()
{
    return \Jankx\Asset\Bucket::instance();
}

function css($handler, $cssUrl = null, $dependences = [], $version = null, $media = 'all', $preload = false)
{
    return call_user_func(
        array(asset(), 'css'),
        $handler,
        $cssUrl,
        $dependences,
        $version,
        $media,
        $preload
    );
}

function js($handler, $jsUrl = null, $dependences = [], $version = null, $isFooterScript = true, $preload = false)
{
    return call_user_func(
        array(asset(), 'js'),
        $handler,
        $jsUrl,
        $dependences,
        $version,
        $isFooterScript,
        $preload
    );
}

function style($cssContent, $media = 'all')
{
    return call_user_func(
        array(asset(), 'style'),
        $cssContent,
        $media
    );
}

function init_script($js, $isHeaderScript = false)
{
    return call_user_func(
        array(asset(), 'script'),
        $js,
        $isHeaderScript
    );
}

function execute_script($jsContent, $autoWrapByScriptTag = false)
{
    return call_user_func(
        array(asset(), 'executeScript'),
        $jsContent,
        $autoWrapByScriptTag
    );
}

function is_registered_asset($handler, $isStylesheet = true)
{
    return call_user_func(
        array(asset(), 'isRegistered'),
        $handler,
        $isStylesheet
    );
}

function call_js($handler) {
}
