<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Example {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $msg = "sup";
        return array("type" => "public", "message" => $msg);
    }
}