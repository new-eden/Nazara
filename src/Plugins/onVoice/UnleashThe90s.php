<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:59
 */

namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use League\Container\Container;
use Monolog\Logger;
use YoutubeDl\Exception\CopyrightException;
use YoutubeDl\Exception\NotFoundException;
use YoutubeDl\Exception\PrivateVideoException;
use YoutubeDl\YoutubeDl;

class UnleashThe90s
{
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        $curl = $container->get("curl");
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
            $video = $dl->download("https://www.youtube.com/watch?v={$song->youtubeid}");
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

        $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) use ($message, $discord, $log, &$audioStreams, $channel, $songFile, $song, $guildID) {
            if (file_exists($songFile)) {
                // Add this audio stream to the array of audio streams
                $audioStreams[$guildID] = $vc;
                $vc->setFrameSize(40)->then(function () use ($vc, &$audioStreams, $guildID, $songFile, $log, $message, $song, $channel) {
                    $vc->setBitrate(128000);
                    $message->reply("Now playing **{$song->title}** by **{$song->artist}** in {$channel->name}");
                    $vc->playFile($songFile, 2)->done(function () use ($vc, &$audioStreams, $guildID) {
                        unset($audioStreams[$guildID]);
                        $vc->close();
                    });
                });
            }
        });
    }
}