<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:28
 */

namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use League\Container\Container;
use Monolog\Logger;

class Reapers {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) use ($message, $discord, $log, &$audioStreams, $channel, $guildID) {
            // Add this audio stream to the array of audio streams
            $audioStreams[$guildID] = $vc;
            $vc->setFrameSize(40)->then(function () use ($vc, &$audioStreams, $guildID) {
                $vc->setBitrate(128000);
                $number = mt_rand(1, 23);
                $file = __DIR__ . "/../../../audio/reapers/{$number}.mp3";
                $vc->playFile($file, 2)->done(function () use ($vc, &$audioStreams, $guildID) {
                    unset($audioStreams[$guildID]);
                    $vc->close();
                });
            });
        });
    }
}