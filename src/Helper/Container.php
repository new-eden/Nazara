<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 18:34
 */

namespace Nazara\Helper;


use League\Container\ReflectionContainer;
use Nazara\Service\SystemServiceProvider;

class Container {
    private $container;

    public function __construct() {
        $this->container = new \League\Container\Container();
        $this->container->delegate(new ReflectionContainer());
        $this->container->add("configFile", __DIR__ . "/../../config/config.php");
        $this->container->addServiceProvider(SystemServiceProvider::class);
    }

    public function getContainer() {
        return $this->container;
    }
}