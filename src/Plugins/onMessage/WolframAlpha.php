<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class WolframAlpha {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $config = $container->get("config");
        $curl = $container->get("curl");
        $appID = $config->get("appID", "wolframalpha");
        unset($parts[0]);
        $query = urlencode(implode(" ", $parts));
        $url = "http://api.wolframalpha.com/v2/query?appid={$appID}&input={$query}";
        $data = json_decode(json_encode(new \SimpleXMLElement($curl->getData($url))), true);
        $result = $data["pod"][1]["subpod"];
        if (!empty($result)) {
            $image = $result["img"]["@attributes"]["src"];
            $text = $result["plaintext"];
            if (strlen($image) > 0) {
                $msg = "{$text}\r\n {$image}";
                $wolfFileName = md5($query);
                file_put_contents(__DIR__ . "/../../../cache/images/{$wolfFileName}.gif", $image);
                $message->getChannelAttribute()->sendFile(__DIR__ . "/../../../cache/image/{$wolfFileName}.gif", "{$wolfFileName}.gif");
            } else {
                $msg = "Result: {$image}";
            }
        } else {
            $msg = "WolframAlpha did not have an answer to your query..";
        }

        return array("type" => "public", "message" => $msg);
    }
}