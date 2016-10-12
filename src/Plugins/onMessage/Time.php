<?php
namespace Nazara\Plugins\onMessage;

use DateTime;
use DateTimeZone;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use League\Container\Container;

class Time {
    public function run(Discord $discord, Container $container, Message $message, $parts) {
        $date = date("d-m-Y");
        $fullDate = date("Y-m-d H:i:s");
        $dateTime = new DateTime($fullDate);
        $et = $dateTime->setTimezone(new DateTimeZone("America/New_York"));
        $et = $et->format("H:i:s");
        $pt = $dateTime->setTimezone(new DateTimeZone("America/Los_Angeles"));
        $pt = $pt->format("H:i:s");
        $utc = $dateTime->setTimezone(new DateTimeZone("UTC"));
        $utc = $utc->format("H:i:s");
        $cet = $dateTime->setTimezone(new DateTimeZone("Europe/Copenhagen"));
        $cet = $cet->format("H:i:s");
        $msk = $dateTime->setTimezone(new DateTimeZone("Europe/Moscow"));
        $msk = $msk->format("H:i:s");
        $aest = $dateTime->setTimezone(new DateTimeZone("Australia/Sydney"));
        $aest = $aest->format("H:i:s");
        $msg = "**Current EVE Time:** {$utc} / **EVE Date:** {$date} / **Los Angeles (PT):** {$pt} / **New York (ET):** {$et} / **Berlin/Copenhagen (CET):** {$cet} / **Moscow (MSK):** {$msk} / **Sydney (AEST):** {$aest}";

        return array("type" => "public", "message" => $msg);
    }
}