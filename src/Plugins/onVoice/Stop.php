<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:39
 */

namespace Nazara\Plugins\onVoice;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use League\Container\Container;
use Monolog\Logger;

class Stop {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        if (isset($audioStreams[$guildID])) {
            // Kill the EVERadio FFMPEG stream if it's running
            if (isset($audioStreams["streaming"][$guildID])) {
                echo "attempting to kill the ffmpeg stream..\n";
                $audioStreams["streaming"][$guildID]->close();
            }
            echo "stopping the stream";
            $audioStreams[$guildID]->stop();
            $audioStreams[$guildID]->close();
            unset($audioStreams[$guildID]);
            $message->reply("Stopping audio playback..");
        }
    }
}