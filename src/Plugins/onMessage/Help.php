<?php
namespace Nazara\Plugins\onMessage;


use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Help {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $bot = $container->get("bot");
        $plugins = $bot->getPlugins();

        //@todo at a replace that replaces %% in the usage part, with whatever the actual prefix is..
        if(count($parts) > 1) {
            $helpPlugin = $parts[1];
            $msg = "**Error**, no such plugin exists..";
            foreach ($plugins as $pluginType) {
                foreach ($pluginType as $pluginName => $pluginInfo) {
                    if($pluginName == $helpPlugin) {
                        $msg = "__**{$pluginName}:**__ {$pluginInfo["description"]}";
                        if (!empty($pluginInfo["usage"])) {
                            $msg .= " / Usage information: {$pluginInfo["usage"]}\n";
                        }
                    }
                }
            }
            return array("type" => "public", "message" => $msg);
        } else {
            $msg = "**Plugins available:** (Command first, then description, For example: !help)\n";
            foreach ($plugins as $pluginType) {
                foreach ($pluginType as $pluginName => $pluginInfo) {
                    $msg .= "__**{$pluginName}:**__ {$pluginInfo["description"]}";
                    if (!empty($pluginInfo["usage"])) {
                        $msg .= " / {$pluginInfo["usage"]}\n";
                    } else {
                        $msg .= "\n";
                    }
                }
            }
            return array("type" => "private", "message" => $msg);
        }
    }
}