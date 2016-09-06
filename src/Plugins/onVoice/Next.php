<?php
namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use League\Container\Container;
use Monolog\Logger;

class Next {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        if (isset($audioStreams[$guildID])) {
            $audioStreams[$guildID]->stop();
            $message->reply("Skipping to the next song..");
        }
    }
}