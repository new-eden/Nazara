<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 07-09-2016
 * Time: 00:35
 */

namespace Nazara\Plugins\onVoice;


use Discord\Discord;
use Discord\Helpers\Process;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Voice\VoiceClient;
use League\Container\Container;
use Monolog\Logger;

class RadioStations {
    public function run(Message $message, Discord $discord, Logger $log, &$audioStreams, Channel $channel, $guildID, Container $container) {
        $explode = explode(" ", $message->content);
        unset($explode[0]);
        $radioStation = implode(" ", $explode);
        // List of radio stations available.
        $radios = array(
            "noisefm" => "http://noisefm.ru:8000/play?icy=http",
            "radiobeats" => "http://streaming.shoutcast.com/RadioBeatsFM",
            "vivafm" => "http://178.32.139.120:8002/stream",
            "dance" => "http://stream.dancewave.online:8080/dance.mp3?icy=http",
            "anrdk" => "http://stream.anr.dk/anr",
            "thevoicedk" => "http://stream.voice.dk/voice128",
            "schlager" => "http://193.34.51.130/schlager_mp3",
            "everadio" => "http://media01.evehost.net:8022/",
            "eurodance" => "http://streaming.radionomy.com/Eurodance-90",
            "djdeant" => "http://anycast.dj-deant.com/?icy=http",
            "amsterdamtranceradio" => "http://185.33.21.112:11029/;?icy=http",
            "psyradio" => "http://81.88.36.44:8030/;?icy=http",
            "classical" => "http://109.123.116.202:8020/stream?icy=http",
            "classicrock" => "http://185.33.22.15:11093/;?icy=http",
            "groovesalad" => "http://ice1.somafm.com/groovesalad-128-mp3",
            "dronezone" => "http://ice1.somafm.com/dronezone-128-mp3",
            "indiepoprocks" => "http://ice1.somafm.com/indiepop-128-mp3",
            "spacestationsoma" => "http://ice1.somafm.com/spacestation-128-mp3",
            "secretagent" => "http://ice1.somafm.com/secretagent-128-mp3",
            "lush" => "http://ice1.somafm.com/lush-128-mp3",
            "underground80s" => "http://ice1.somafm.com/u80s-128-mp3",
            "deepspaceone" => "http://ice1.somafm.com/deepspaceone-128-mp3",
            "leftcoast70s" => "http://ice1.somafm.com/seventies-128-mp3",
            "bootliquor" => "http://ice1.somafm.com/bootliquor-128-mp3",
            "thetrip" => "http://ice1.somafm.com/thetrip-128-mp3",
            "suburbsofgoa" => "http://ice1.somafm.com/suburbsofgoa-128-mp3",
            "bagelradio" => "http://ice1.somafm.com/bagel-128-mp3",
            "beatblender" => "http://ice1.somafm.com/beatblender-128-mp3",
            "defconradio" => "http://ice1.somafm.com/defcon-128-mp3",
            "sonicuniverse" => "http://ice1.somafm.com/sonicuniverse-128-mp3",
            "folkforward" => "http://ice1.somafm.com/folkfwd-128-mp3",
            "poptron" => "http://ice1.somafm.com/poptron-128-mp3",
            "illinoisstreetlounge" => "http://ice1.somafm.com/illstreet-128-mp3",
            "fluid" => "http://ice1.somafm.com/fluid-128-mp3",
            "thistleradio" => "http://ice1.somafm.com/thistle-128-mp3",
            "seveninchsoil" => "http://ice1.somafm.com/7soul-128-mp3",
            "digitalis" => "http://ice1.somafm.com/digitalis-128-mp3",
            "cliqhopidm" => "http://ice1.somafm.com/cliqhop-128-mp3",
            "missioncontrol" => "http://ice1.somafm.com/missioncontrol-128-mp3",
            "dubstepbeyond" => "http://ice1.somafm.com/dubstep-128-mp3",
            "covers" => "http://ice1.somafm.com/covers-128-mp3",
            "thesilentchannel" => "http://ice1.somafm.com/silent-128-mp3",
            "blackrockfm" => "http://ice1.somafm.com/brfm-128-mp3",
            "doomed" => "http://ice1.somafm.com/doomed-128-mp3",
            "sf1033" => "http://ice1.somafm.com/sf1033-128-mp3",
            "earwaves" => "http://ice1.somafm.com/earwaves-128-mp3",
            "metaldetector" => "http://ice1.somafm.com/metal-128-mp3",
            "90ernedk" => "http://194.16.21.232/197_dk_aacp",
            "novadk" => "http://stream.novafm.dk/nova128",
            "radio100" => "http://onair.100fmlive.dk/100fm_live.mp3",
            "nrjca" => "http://8743.live.streamtheworld.com/CKMFFMAAC",
            "nrjse" => "http://194.16.21.227/nrj_se_aacp",
            "nrjno" => "http://stream.p4.no/nrj_mp3_mq",
            "nrjde" => "http://95.81.155.20/8032/nrj_145202.mp3",
            "limfjorddk" => "http://media.limfjordnetradio.dk/limfjord128",
            "alfadk" => "http://netradio.radioalfa.dk/",
            "partyzonedk" => "http://stream1.partyzone.nu/mp3",
        );
        // Defaults
        $radioNames = array();
        $url = "";
        // Populate the radioNames, and also get the URL if the person actually defined one
        foreach($radios as $radioName => $radioURL) {
            $radioNames[] = ucfirst($radioName);
            if (strtolower($radioName) == strtolower($radioStation)) {
                $url = $radioURL;
            }
        }
        if(!$url)
            $message->reply("You can listen to the following radios: " . implode(", ", $radioNames));
        if (!empty($url)) {
            $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) use ($message, $discord, $log, &$audioStreams, $channel, $url, $guildID) {
                // Add this audio stream to the array of audio streams
                $audioStreams[$guildID] = $vc;
                // Set the bitrate and framesize
                $vc->setBitrate(128000);
                $vc->setFrameSize(40);
                $params = [
                    'ffmpeg',
                    '-i', $url,
                    '-f', 's16le',
                    '-acodec', 'pcm_s16le',
                    '-loglevel', 0,
                    '-ar', 48000,
                    '-ac', 2,
                    '-tune', 'zerolatency',
                    'pipe:1',
                ];
                $audioStreams["streaming"][$guildID] = new Process(implode(" ", $params));
                $audioStreams["streaming"][$guildID]->start($discord->loop);
                $vc->playRawStream($audioStreams["streaming"][$guildID]->stdout)->done(function () use (&$audioStreams, $vc, $guildID) {
                    $audioStreams["streaming"][$guildID]->close();
                    unset($audioStreams[$guildID]);
                    $vc->close();
                });
            });
        }
    }
}