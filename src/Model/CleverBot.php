<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 21:11
 */

namespace Nazara\Model;


use MongoDB\Client;
use Nazara\Helper\Mongo;
use Nazara\Lib\Config;
use Nazara\Lib\cURL;

class CleverBot extends Mongo {
    public $collectionName = "cleverbot";
    public $databaseName = "nazara";
    private $curl;
    private $config;

    public function __construct(Config $config, Client $mongodb, cURL $curl) {
        parent::__construct($config, $mongodb);
        $this->curl = $curl;
        $this->config = $config;
    }

    public function createCleverBotForGuild($guildID) {
        $exists = $this->getIDForGuild($guildID);
        if(!empty($exists))
            return $exists["cleverBotID"];

        $result = $this->curl->sendData("https://cleverbot.io/1.0/create", array("user" => $this->config->get("user", "cleverbot"), "key" => $this->config->get("key", "cleverbot")));

        if($result) {
            $result = @json_decode($result);
            $nick = $result->nick ?? false;

            if($nick) {
                $data = array(
                    "cleverBotID" => $nick,
                    "guildID" => $guildID
                );

                $this->collection->replaceOne(array("guildID" => $guildID), $data, array("upsert" => true));
                return $nick;
            }
        }

        return null;
    }

    public function getIDForGuild($guildID) {
        return $this->collection->findOne(array("guildID" => $guildID));
    }

    public function getMessage($inputMessage, $cleverBotID) {
        $response = $this->curl->sendData("https://cleverbot.io/1.0/ask", array("user" => $this->config->get("user", "cleverbot"), "key" => $this->config->get("key", "cleverbot"), "nick" => $cleverBotID, "text" => $inputMessage));
        if($response) {
            $resp = @json_decode($response);
            $reply = $resp->response ?? false;

            if($reply) {
                return $reply;
            }
        }
    }

    public function messageCount() {
        return $this->collection->count();
    }
}