<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 23:45
 */

namespace Nazara\Service;


use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class SystemPluginServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {

    protected $provides = array();

    public function boot() {
        $bot = $this->getContainer()->get("bot");

        // Regular plugins
        $bot->addPlugin("onMessage", "help", "\\Nazara\\Plugins\\onMessage\\Help", 1, "Show the help information", "%%help or %%help <type> for specific plugin help", null);
        $bot->addPlugin("onMessage", "porn", "\\Nazara\\Plugins\\onMessage\\Porno", 1, "Shows pornographic images on Discord..", "%%porn <type>, for example %%porn redheads", null);
        $bot->addPlugin("onMessage", "about", "\\Nazara\\Plugins\\onMessage\\About", 1, "Shows information about the bot and it's creator...", "%%about", null);
        $bot->addPlugin("onMessage", "wolf", "\\Nazara\\Plugins\\onMessage\\WolframAlpha", 1, "Queries WolframAlpha, and gets you a result...", "%%wolf <query>", null);
        $bot->addPlugin("onMessage", "coinflip", "\\Nazara\\Plugins\\onMessage\\CoinFlip", 1, "Flips a coin, and tells you what it landed as...", "%%coinflip", null);
        $bot->addPlugin("onMessage", "eightball", "\\Nazara\\Plugins\\onMessage\\EightBall", 1, "Shakes the magic eightball, and tells you the result...", "%%eightball", null);
        $bot->addPlugin("onMessage", "meme", "\\Nazara\\Plugins\\onMessage\\Meme", 1, "Dank memes...", "%%meme", null);
        $bot->addPlugin("onMessage", "time", "\\Nazara\\Plugins\\onMessage\\Time", 1, "Tells you the current time, in multiple timezones...", "%%time", null);
        $bot->addPlugin("onMessage", "tq", "\\Nazara\\Plugins\\onMessage\\Tranquility", 1, "Shows the current status of the Tranquility Cluster, as reported by CREST...", "%%tq", null);
        $bot->addPlugin("onMessage", "pc", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%pc <item>", null);
        $bot->addPlugin("onMessage", "jita", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%jita <item>", null);
        $bot->addPlugin("onMessage", "amarr", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%amarr <item>", null);
        $bot->addPlugin("onMessage", "dodixie", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%dodixie <item>", null);
        $bot->addPlugin("onMessage", "hek", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%hek <item>", null);
        $bot->addPlugin("onMessage", "rens", "\\Nazara\\Plugins\\onMessage\\PriceCheck", 1, "Shows EVE market information for an item...", "%%rens <item>", null);
        $bot->addPlugin("onMessage", "join", "\\Nazara\\Plugins\\onMessage\\Join", 1, "Shows an invite link", "%%join", null);

        // Voice Plugins
        $bot->addPlugin('onVoice', 'horn', "\\Nazara\\Plugins\\onVoice\\Horns", 1, 'Horns. Just horns..', '', null);
        $bot->addPlugin('onVoice', 'reapers', "\\Nazara\\Plugins\\onVoice\\Reapers", 1, 'Plays a random quote from Sovereign', '', null);
        $bot->addPlugin('onVoice', 'warnings', "\\Nazara\\Plugins\\onVoice\\EveWarnings", 1, 'Plays a random warning sound from EVE-Online', '', null);
        $bot->addPlugin('onVoice', 'terror', "\\Nazara\\Plugins\\onVoice\\Terror", 1, 'It\'s terror time!', '', null);
        $bot->addPlugin('onVoice', 'radio', "\\Nazara\\Plugins\\onVoice\\RadioStations", 1, 'Keeps on playing a Radio station, till you go !stop', '', null);
        $bot->addPlugin('onVoice', 'unleashthe90s', "\\Nazara\\Plugins\\onVoice\\UnleashThe90s", 1, 'Plays a random 90s song', '', null);
        $bot->addPlugin('onVoice', 'radio90s', "\\Nazara\\Plugins\\onVoice\\Radio90s", 1, 'Keeps on playing 90s songs, till you go !stop', '', null);

        // Audio Controls
        $bot->addPlugin('onVoice', 'pause', "\\Nazara\\Plugins\\onVoice\\Pause", 1, 'Pauses audio playback', '', null);
        $bot->addPlugin('onVoice', 'stop', "\\Nazara\\Plugins\\onVoice\\Stop", 1, 'Stops audio playback', '', null);
        $bot->addPlugin('onVoice', 'next', "\\Nazara\\Plugins\\onVoice\\Next", 1, 'Goes to the next track if radio90s is playing', '', null);
        $bot->addPlugin('onVoice', 'unpause', "\\Nazara\\Plugins\\onVoice\\UnPause", 1, 'Resumes audio playback', '', null);
        $bot->addPlugin('onVoice', 'resume', "\\Nazara\\Plugins\\onVoice\\UnPause", 1, 'Resumes audio playback', '', null);

        // Timer plugins
        $bot->addPlugin("onTimer", "seatSiphons", "\\Nazara\\Plugins\\onTimer\\seatSiphons", 0, "", "", 1800);
    }

    public function register() {
    }
}