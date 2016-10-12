<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class CoinFlip {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $sides = ["Heads", "Tails"];
        $msg = "The result of the coinflip is: " . $sides[array_rand($sides)];
        return array("type" => "public", "message" => $msg);
    }
}