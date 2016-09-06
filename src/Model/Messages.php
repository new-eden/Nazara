<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 21:11
 */

namespace Nazara\Model;


use Nazara\Helper\Mongo;

class Messages extends Mongo {
    public $collectionName = "messages";
    public $databaseName = "nazara";

    public function storeMessage(string $message, int $messageID, string $from, $fromNickname, int $fromID, string $postedTime, string $channelName, int $channelID, string $guildName, int $guildID) {
        $data = array(
            "messageID" => $messageID,
            "message" => $message,
            "from" => $from,
            "nickname" => $fromNickname,
            "fromID" => $fromID,
            "timePosted" => $this->makeTimeFromDateTime($postedTime),
            "channelName" => $channelName,
            "channelID" => $channelID,
            "guildName" => $guildName,
            "guildID" => $guildID
        );

        $this->collection->insertOne($data, array("upsert" => true));
    }

    public function messageCount() {
        return $this->collection->count();
    }
}