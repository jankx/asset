<?php
namespace Jankx\Asset\Interfaces;

interface ItemInterface
{
    public function callDependences($dependences);

    public function call($handler = null);

    public function register();
}
