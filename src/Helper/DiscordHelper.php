<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 20:44
 */

namespace Nazara\Helper;


use Discord\Parts\Guild\Guild;

class DiscordHelper {
    public function getMemberCount(\Discord\Discord $discord) {
        $memberCount = 0;
        foreach($discord->guilds->all() as $guild)
            $memberCount += $guild->member_count;

        return $memberCount;
    }

    public function getGuildCount(\Discord\Discord $discord) {
        return $discord->guilds->count();
    }

    public function getGuild(\Discord\Discord $discord, $guildID) {
        foreach ($discord->guilds as $guild) {
            if($guild->id == $guildID) {
                return $guild;
            }
        }
    }

    public function getChannelsForGuild(\Discord\Discord $discord, $guildID) {
        foreach($discord->guilds as $guild) {
            if($guild->id == $guildID) {
                return $guild->channels;
            }
        }
    }

    public function getChannel(\Discord\Discord $discord, $channelID) {
        foreach ($discord->guilds as $guild) {
            foreach($guild->channels as $channel) {
                if($channel->id == $channelID) {
                    return $channel;
                }
            }
        }
    }

    public function getMember(\Discord\Discord $discord, $memberID) {
        foreach($discord->guilds as $guild) {
            foreach($guild->members as $member) {
                if($member->user->id == $memberID)
                    return $member;
            }
        }
    }
}