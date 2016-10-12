<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;
use Nazara\Lib\cURL;

class PriceCheck {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $curl = $container->get("curl");
        $config = $container->get("config");
        $mongo = $container->get("mongo");
        $collection = $mongo->selectCollection("ccp", "typeIDs");
        $prefix = $config->get("prefix", "bot"); //@todo make it so this prefix can be gotten pr. server

        $system = str_replace($prefix, "", $parts[0]);
        unset($parts[0]);
        $item = implode(" ", $parts);

        // Valid SolarSystems
        $validSystems = array("jita" => 30000142, "amarr" => 30002187, "hek" => 30002053, "dodixie" => 30002659, "rens" => 30002510, "pc" => 0);
        if($system == "pc") {
            $system = "Global";
            $solarSystemID = 0;
        }
        else
            $solarSystemID = $validSystems[strtolower($system)] ?? null;

        // Define quickLookups, so we don't use the DB unless we have to..
        $quickLookups = array(
            "plex" => array("typeName" => "30 Day Pilot's License Extension (PLEX)", "typeID" => 29668),
            "injector" => array("typeName" => "Skill Injector", "typeID" => 40520),
            "extractor" => array("typeName" => "Skill Extractor", "typeID" => 40519)
        );

        if(isset($quickLookups[$item])) {
            $itemData = $quickLookups[$item];
            $marketData = $this->getData($itemData["typeID"], $solarSystemID, $curl);
            $ucItem = ucfirst($item);
            $ucSystem = ucfirst($system);
            $msg = "{$ucItem} (**{$ucSystem}**) - (High/Avg/Low) ";
            $msg .= "\n**Buy:** {$marketData["highBuy"]} isk | {$marketData["avgBuy"]} isk | {$marketData["lowBuy"]} isk";
            $msg .= "\n**Sell:** {$marketData["highSell"]} isk | {$marketData["avgSell"]} isk | {$marketData["lowSell"]} isk";
        } else {
            $results = $collection->find(
                array("\$text" => array("\$search" => "\"$item\"")),
                array(
                    "score" => array("\$meta" => "textScore"),
                    "sort" => array("\$score" => -1),
                    "projection" => array("_id" => 0),
                    "limit" => 10)
            )->toArray();

            $msg = "Error, found more than one result, please be more specific, and pick from one of the items below\n";
            $typeID = null;
            foreach($results as $result) {
                if(strtolower(trim($result["name"]["en"])) == strtolower($item)) {
                    $typeID = $result["typeID"];
                } else {
                    $msg .= "{$result["name"]["en"]}";
                }
            }

            if($typeID != null) {
                $marketData = $this->getData($typeID, $solarSystemID, $curl);
                $ucItem = ucfirst($item);
                $ucSystem = ucfirst($system);
                $msg = "{$ucItem} (**{$ucSystem}**) - (High/Avg/Low) ";
                $msg .= "\n**Buy:** {$marketData["highBuy"]} isk | {$marketData["avgBuy"]} isk | {$marketData["lowBuy"]} isk";
                $msg .= "\n**Sell:** {$marketData["highSell"]} isk | {$marketData["avgSell"]} isk | {$marketData["lowSell"]} isk";
            }
        }

        return array("type" => "public", "message" => $msg);
    }

    private function getData($itemID, $solarSystemID, cURL $curl) {
        if($solarSystemID == null || $solarSystemID == 0)
            $url = "https://api.eve-central.com/api/marketstat/json?typeid={$itemID}";
        else
            $url ="https://api.eve-central.com/api/marketstat/json?usesystem={$solarSystemID}&typeid={$itemID}";

        $data = json_decode($curl->getData($url));

        $buy = $data[0]->buy;
        $sell = $data[0]->sell;
        return array(
            "lowBuy" => number_format($buy->min, 2),
            "avgBuy" => number_format($buy->avg, 2),
            "highBuy" => number_format($buy->max, 2),
            "lowSell" => number_format($sell->min, 2),
            "avgSell" => number_format($sell->avg, 2),
            "highSell" => number_format($sell->max, 2),
        );
    }
}