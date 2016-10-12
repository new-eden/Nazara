<?php
namespace Nazara\Plugins\onMessage;

use DateTime;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class About {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $startTime = $container->get("startTime");
        $discordHelper = $container->get("discordHelper");

        $guildCount = $discordHelper->getGuildCount($discord);
        $memberCount = $discordHelper->getMemberCount($discord);

        $time1 = new DateTime(date("Y-m-d H:i:s", $startTime));
        $time2 = new DateTime(date("Y-m-d H:i:s"));
        $interval = $time1->diff($time2);

        $msg = "```I am the vanguard of your destruction. This exchange is just beginning...

Author: Karbowiak (Discord ID: 118440839776174081)
Library: DiscordPHP (https://github.com/teamreflex/DiscordPHP\)
Git Repo: https://gitlab.com/neweden/Nazara\

Statistics:
Guild/Server Count: {$guildCount}
Member Count: {$memberCount}
Memory Usage: ~" . round(memory_get_usage() / 1024 / 1024, 3) . "MB (Peak: ~" . round(memory_get_peak_usage() / 1024 / 1024, 3) . "MB)
Uptime: " . $interval->y . " Year(s), " . $interval->m . " Month(s), " . $interval->d . " Day(s), " . $interval->h . " Hour(s), " . $interval->i . " Minute(s), " . $interval->s . " second(s).
```";

        return array("type" => "public", "message" => $msg);
    }
}