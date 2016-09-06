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

        // Voice Plugins
        $bot->addPlugin('onVoice', 'horn', "\\Nazara\\Plugins\\onVoice\\Horns", 1, 'Horns. Just horns..', '', null);
        $bot->addPlugin('onVoice', 'reapers', "\\Nazara\\Plugins\\onVoice\\Reapers", 1, 'Plays a random quote from Sovereign', '', null);
        $bot->addPlugin('onVoice', 'warnings', "\\Nazara\\Plugins\\onVoice\\EveWarnings", 1, 'Plays a random warning sound from EVE-Online', '', null);
        $bot->addPlugin('onVoice', 'radio', "\\Nazara\\Plugins\\onVoice\\RadioStations", 1, 'Keeps on playing a Radio station, till you go !stop', '', null);
        $bot->addPlugin('onVoice', 'unleashthe90s', "\\Nazara\\Plugins\\onVoice\\UnleashThe90s", 1, 'Plays a random 90s song', '', null);
        $bot->addPlugin('onVoice', 'radio90s', "\\Nazara\\Plugins\\onVoice\\Radio90s", 1, 'Keeps on playing 90s songs, till you go !stop', '', null);

        // Audio Controls
        $bot->addPlugin('onVoice', 'pause', "\\Nazara\\Plugins\\onVoice\\Pause", 1, 'Pauses audio playback', '', null);
        $bot->addPlugin('onVoice', 'stop', "\\Nazara\\Plugins\\onVoice\\Stop", 1, 'Stops audio playback', '', null);
        $bot->addPlugin('onVoice', 'next', "\\Nazara\\Plugins\\onVoice\\Next", 1, 'Goes to the next track if radio90s is playing', '', null);
        $bot->addPlugin('onVoice', 'unpause', "\\Nazara\\Plugins\\onVoice\\UnPause", 1, 'Resumes audio playback', '', null);
        $bot->addPlugin('onVoice', 'resume', "\\Nazara\\Plugins\\onVoice\\UnPause", 1, 'Resumes audio playback', '', null);
    }

    public function register() {
    }
}