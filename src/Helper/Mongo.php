<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 19:13
 */

namespace Nazara\Helper;


use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use Nazara\Lib\Config;

class Mongo {
    public $collectionName = "";
    public $databaseName = "";
    protected $mongodb;
    protected $collection;

    public function __construct(Config $config, Client $mongodb) {
        $this->mongodb = $mongodb;
        $db = !empty($this->databaseName) ? $this->databaseName : $config->get("dbName", "mongodb");
        $this->collection = $mongodb->selectCollection($db, $this->collectionName);
    }

    public function makeTimeFromDateTime($dateTime): UTCDatetime {
        $unixTime = strtotime($dateTime);
        $milliseconds = $unixTime * 1000;

        return new UTCDatetime($milliseconds);
    }

    public function makeTimeFromUnixTime($unixTime): UTCDatetime {
        $milliseconds = $unixTime * 1000;
        return new UTCDatetime($milliseconds);
    }

}