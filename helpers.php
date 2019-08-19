<?php
function asset()
{
    return \Jankx\Asset\Bucket::instance();
}

function jankx_core_asset_url($path)
{
    return sprintf(
        '%s/asset/resources/%s',
        Jankx::vendorUrl(),
        $path
    );
}

function css($handler, $cssUrl = null, $dependences = [], $version = null, $media = 'all')
{
    return call_user_func(
        array(asset(), 'css'),
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
        array(asset(), 'js'),
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

function execute_script($jsContent)
{
    return call_user_func(
        array(asset(), 'executeScript'),
        $jsContent
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
