<?php

namespace Jankx\Asset;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

interface AssetInterface
{
    public function call();

    public function register();
}
