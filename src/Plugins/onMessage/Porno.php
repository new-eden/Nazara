<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 03:33
 */

namespace Nazara\Plugins\onMessage;


use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Porno {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $curl = $container->get("curl");
        $config = $container->get("config");

        $type = @$parts[1];
        $categoryNames = array();
        $categories = array(
            "redheads" => array(
                "https://api.imgur.com/3/gallery/r/redheads/time/all/",
                "https://api.imgur.com/3/gallery/r/ginger/time/all/",
                "https://api.imgur.com/3/gallery/r/FireCrotch/time/all/"
            ),
            "blondes" => "https://api.imgur.com/3/gallery/r/blondes/time/all/",
            "asians" => "https://api.imgur.com/3/gallery/r/AsiansGoneWild/time/all/",
            "gonewild" => "https://api.imgur.com/3/gallery/r/gonewild/time/all/",
            "realgirls" => "https://api.imgur.com/3/gallery/r/realgirls/time/all/",
            "palegirls" => "https://api.imgur.com/3/gallery/r/palegirls/time/all/",
            "gif" => "https://api.imgur.com/3/gallery/r/NSFW_GIF/time/all/",
            "lesbians" => "https://api.imgur.com/3/gallery/r/lesbians/time/all/",
            "tattoos" => "https://api.imgur.com/3/gallery/r/Hotchickswithtattoos/time/all/",
            "mgw" => "https://api.imgur.com/3/gallery/r/MilitaryGoneWild/time/all/",
            "amateur" => "https://api.imgur.com/3/gallery/r/AmateurArchives/time/all/",
            "college" => "https://api.imgur.com/3/gallery/r/collegesluts/time/all/",
            "bondage" => "https://api.imgur.com/3/gallery/r/bondage/time/all/",
            "milf" => "https://api.imgur.com/3/gallery/r/milf/time/all/",
            "freckles" => "https://api.imgur.com/3/gallery/r/FreckledGirls/time/all/",
            "cosplay" => "https://api.imgur.com/3/gallery/r/cosplay/time/all/",
            "tits" => "https://api.imgur.com/3/gallery/r/boobs/time/all/",
            "ass" => "https://api.imgur.com/3/gallery/r/ass/time/all/",
            "food" => "https://api.imgur.com/3/gallery/r/foodporn/time/all/",
            "gifrecipes" => "https://api.imgur.com/3/gallery/r/gifrecipes/time/all/",
            "bbw" => "https://api.imgur.com/3/gallery/r/bbw/time/all/",
            "dongs" => "https://api.imgur.com/3/gallery/r/penis/time/all/",
            "innie" => "https://api.imgur.com/3/gallery/r/innie/time/all/",
            "donald" => array(
                "https://api.imgur.com/3/gallery/r/donald_trump/time/all/",
                "https://api.imgur.com/3/gallery/t/trump/time/all/"
            ),
            "eve" => "https://api.imgur.com/3/album/KaVsO",
        );

        foreach($categories as $catName => $catURL) {
            $categoryNames[] = ucfirst($catName);
            if(strtolower($type) == strtolower($catName)) {
                if(is_array($catURL))
                    $url = $catURL[array_rand($catURL)];
                else
                    $url = $catURL;
            }
        }

        if (!empty($url)) {
            // Select a random url
            $clientID = $config->get("clientID", "imgur");

            $headers = array();
            $headers[] = "Content-type: application/json";
            $headers[] = "Authorization: Client-ID {$clientID}";
            $data = $curl->getData($url, $headers);
            if ($data) {
                $json = json_decode($data, true)["data"];

                if($json["in_gallery"] === false && $json["section"] === null)
                    $type = "album";
                else
                    $type = "gallery";

                if($type == "gallery") {
                    $img = $json[array_rand($json)];
                    if(!empty($img["gifv"])) $imageURL = $img["gifv"]; else
                        $imageURL = $img["link"];
                }
                else {
                    $img = $json["images"][array_rand($json["images"])];
                    if(!empty($img["gifv"])) $imageURL = $img["gifv"]; else
                        $imageURL = $img["link"];
                }
                $msg = "**Title:** {$img["title"]} | **Section:** {$img["section"]} | **url:** {$imageURL}";
                return array("type" => "public", "message" => $msg);
            }
        } else {
            $msg = "No endpoint selected. Currently available are: " . implode(", ", $categoryNames);
            return array("type" => "public", "message" => $msg);
        }
    }
}
