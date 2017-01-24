<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Join {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $msg = "Sorry, i can't join servers myself anymore. You gotta click the following link, and invite me to your server: https://discordapp.com/oauth2/authorize?client_id=249261137542119424&scope=bot&permissions=36703232";
        return array("type" => "public", "message" => $msg);
    }
}