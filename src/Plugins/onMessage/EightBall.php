<?php
namespace Nazara\Plugins\onMessage;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class EightBall {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $choices = array(
            'It is certain',
            'It is decidedly so',
            'Without a doubt',
            'Yes, definitely',
            'You may rely on it',
            'As I see it, yes',
            'Most likely',
            'More than likely',
            'Outlook good',
            'Yes',
            'No',
            'Lol no',
            'Signs point to, yes',
            'Reply hazy, try again',
            'Ask again later',
            'I Better not tell you now',
            'I Cannot predict now',
            'Concentrate and ask again',
            'Don\'t count on it',
            'My reply is no',
            'My sources say no',
            'Outlook not so good',
            'Very doubtful',
        );

        $msg = $choices[array_rand($choices)];
        return array("type" => "public", "message" => $msg);
    }
}