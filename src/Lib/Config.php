<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 19:14
 */

namespace Nazara\Lib;


class Config {
    /**
     * @var
     */
    private $config;

    /**
     * Config constructor.
     * @param $configFile
     */
    public function __construct($configFile) {
        $this->loadConfig($configFile);
    }

    /**
     * @param $configFile
     */
    public function loadConfig($configFile) {
        if (!file_exists(realpath($configFile))) {
            return;
        }
        $this->config = array_change_key_case(include($configFile), \CASE_LOWER);
    }


    /**
     * @param string $key
     * @param string|null $type
     * @param string|null $default
     * @return string
     */
    public function get(string $key, string $type = null, string $default = null): string {
        $type = strtolower($type);

        if (!empty($this->config[$type][$key])) {
            return (string) $this->config[$type][$key];
        }

        return (string) $default;
    }


    /**
     * @param string|null $type
     * @return array
     */
    public function getAll(string $type = null): array {
        $type = strtolower($type);

        if (!empty($this->config[$type])) {
            return $this->config[$type];
        }

        return array();
    }
}