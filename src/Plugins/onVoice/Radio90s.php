<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:52
 */

namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use League\Container\Container;
use Monolog\Logger;
use Nazara\Lib\cURL;
use YoutubeDl\Exception\CopyrightException;
use YoutubeDl\Exception\NotFoundException;
use YoutubeDl\Exception\PrivateVideoException;
use YoutubeDl\YoutubeDl;

class Radio90s {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        $curl = $container->get("curl");
        $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) use ($message, $discord, $log, &$audioStreams, $channel, $guildID, $curl) {
            // Add this audio stream to the array of audio streams
            $audioStreams[$guildID] = $vc;
            // Set the bitrate to 128Kbit
            $vc->setBitrate(128000);
            $vc->setFrameSize(40);
            $tickQueue = function () use (&$tickQueue, &$vc, &$message, &$channel, &$curl, &$log, &$audioStreams, $guildID) {
                // Get the song we'll be playing this round
                $data = $this->getSong($curl, $log);
                $song = $data["songData"];
                $songFile = $data["songFile"];
                // Do we really want it to spam the origin channel with what song we're playing all the time?
                $message->getChannelAttribute()->sendMessage("Now playing **{$song->title}** by **{$song->artist}** in {$channel->name}");
                $log->addInfo("Now playing **{$song->title}** by **{$song->artist}** in {$channel->name}");
                $vc->playFile($songFile)->done(function () use (&$tickQueue, $vc, &$log, &$audioStreams, $guildID) {
                    if (isset($audioStreams[$guildID])) {
                        $log->addInfo("Going to next song..");
                        $vc->stop();
                        $tickQueue();
                    }
                });
            };
            if (isset($audioStreams[$guildID])) {
                $tickQueue();
            }
        });
    }

    private function getSong(cURL $curl, Logger $log) {
        retry:
        // Get a random song from the 90sbutton playlist
        $playlist = json_decode($curl->getData("http://the90sbutton.com/playlist.php"));
        $song = $playlist[array_rand($playlist)];
        // Now get the mp3 from
        $songFile = __DIR__ . "/../../../cache/songs/{$song->youtubeid}.mp3";
        $dl = new YoutubeDl([
            "audio-format" => "mp3",
            "extract-audio" => true,
            "audio-quality" => 0,
            "output" => $songFile
        ]);
        try {
            $log->addNotice("Downloading {$song->title} by {$song->artist}");
            $dl->download("https://www.youtube.com/watch?v={$song->youtubeid}");
        } catch (NotFoundException $e) {
            $log->addError("Error: the song was not found: {$e->getMessage()}");
            goto retry;
        } catch (PrivateVideoException $e) {
            $log->addError("Error: song has been made private: {$e->getMessage()}");
            goto retry;
        } catch (CopyrightException $e) {
            $log->addError("Error: song is under copyright: {$e->getMessage()}");
            goto retry;
        } catch (\Exception $e) {
            $log->addError("Error: {$e->getMessage()}");
            goto retry;
        }
        if (file_exists($songFile)) {
            return array("songFile" => $songFile, "songData" => $song);
        } else {
            goto retry;
        }
    }
}