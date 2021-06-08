<?php
namespace Jankx\Asset;

use Jankx\Asset\AssetManager;

class Cache
{
    protected static $globalCss = array();
    protected static $globalJs = array();

    protected static function generateAssetFilePath()
    {
    }

    public static function addGlobalCss($css)
    {
        if (empty($css)) {
            return;
        }
        static::$globalCss[] = $css;
    }

    public static function addGlobalJs($js)
    {
    }

    public static function addCss($css, $type, $object_id = null)
    {
    }

    public static function addJs($js, $type, $object_id = null)
    {
    }

    protected static function loadGlobalCss()
    {
        if (!has_action('jankx_asset_generate_global_css_cache')) {
            add_action('jankx_asset_generate_global_css_cache', function ($globalCss) {
                $cacheDir = rtrim(JANKX_CACHE_DIR, '/');
                $globalCssFile = sprintf('%s/global.css', $cacheDir);
                if (!file_exists($globalCssFile)) {
                    if (!file_exists($cacheDir)) {
                        mkdir($cacheDir, 0755, true);
                    }
                    $h = fopen($globalCssFile, 'w');
                    fwrite($h, implode("\n", $globalCss));
                    fclose($h);
                }
                wp_enqueue_style(
                    'jankx-css-global',
                    sprintf('%s/global.css', rtrim(JANKX_CACHE_DIR_URL, '/')),
                    array(),
                    AssetManager::VERSION
                );
            });
        }
        do_action('jankx_asset_generate_global_css_cache', static::$globalCss);
    }

    public static function load()
    {
        static::loadGlobalCss();
    }

    public static function globalCssIsExists()
    {
        return apply_filters(
            'jankx_asset_global_css_cache_exists',
            file_exists(sprintf('%s/global.css', rtrim(JANKX_CACHE_DIR, '/')))
        );
    }
}
