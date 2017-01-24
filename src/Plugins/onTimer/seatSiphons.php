<?php
namespace Nazara\Plugins\onTimer;

use DateTime;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class seatSiphons {
    public function run(Container $container) {
	return;
        $config = $container->get("config");
        $curl = $container->get("curl");
        $type = "siphon";
        $msg = array();
        $seatInfo = $config->getAll("seat");
        $channelID = $seatInfo["channels"][$type];
        $corporationID = $seatInfo["corporationID"];

        // Looping over all the starbases..
        $url = $seatInfo["location"] . "/api/v1/corporation/starbases/{$corporationID}";
        $starbases = json_decode($curl->getData($url, array("X-Token" => $seatInfo["apikey"], "Accept" => "application/json")));

        foreach($starbases as $starbase) {
            $starbaseData = json_decode($curl->getData($seatInfo["location"] . "/api/v1/corporation/starbases/{$corporationID}/{$starbase->itemID}", array("X-Token" => $seatInfo["apikey"], "Accept" => "application/json")));
            foreach($starbaseData->modules as $data) {
                if($data->detail->typeID == 14343) {
                    $siloContains = $data->used_volume;
                    if($siloContains % 100 && $starbaseData->moonName != "C1XD-X VI - Moon 1") {
                        $msg[] = "Possible siphon detected on {$starbaseData->starbaseTypeName} in {$starbaseData->moonName}";
                    }
                }
            }
        }

        return array("type" => "public", "channelID" => $channelID, "message" => $msg);
    }
}
