<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Tranquility {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $curl = $container->get("curl");
        $crestData = json_decode($curl->getData("https://crest-tq.eveonline.com/"), true);
        $tqStatus = isset($crestData["serviceStatus"]["eve"]) ? $crestData["serviceStatus"]["eve"] : "offline";
        $tqOnline = (int)$crestData["userCounts"]["eve"];
        $msg = "**TQ Status:** {$tqStatus} with {$tqOnline} users online.";
        return array("type" => "public", "message" => $msg);
    }
}