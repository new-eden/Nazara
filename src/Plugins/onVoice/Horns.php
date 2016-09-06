<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 23:46
 */

namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use League\Container\Container;
use Monolog\Logger;

class Horns {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        $discord->joinVoiceChannel($channel, false, true, $log)->then(function(VoiceClient $vc) use ($message, $discord, $log, &$audioStreams, $channel, $guildID) {
            $audioStreams[$guildID] = $vc;

            $vc->setFrameSize(40)->then(function() use ($vc, &$audioStreams, $guildID) {
                $vc->setBitrate(128000);
                $number = mt_rand(1, 6);
                $file = __DIR__ . "/../../../audio/horns/{$number}.mp3";
                $vc->playFile($file, 2)->done(function() use ($vc, &$audioStreams, $guildID) {
                    unset($audioStreams[$guildID]);
                    $vc->close();
                });
            });
        });
    }
}