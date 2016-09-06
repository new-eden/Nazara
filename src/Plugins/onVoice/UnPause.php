<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:40
 */

namespace Nazara\Plugins\onVoice;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use League\Container\Container;
use Monolog\Logger;

class UnPause {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        if (isset($audioStreams[$guildID])) {
            $audioStreams[$guildID]->unpause();
            $message->reply("Resuming audio playback..");
        }
    }
}