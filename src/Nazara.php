<?php
namespace Nazara;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Game;
use Discord\Parts\User\User;
use Discord\Parts\WebSockets\PresenceUpdate;
use Discord\Repository\Guild\ChannelRepository;
use Discord\WebSockets\Event;
use League\Container\Container;
use React\EventLoop\StreamSelectLoop;

class Nazara {
    public $websocket;
    protected $loop;
    protected $discord;
    protected $discordHelper;
    protected $messages;
    protected $container;
    protected $log;
    protected $config;
    protected $users;
    protected $cleverbot;
    private $onMessage = array();
    private $onVoice = array();
    private $onTimer = array();
    private $audioStreams = array();
    private $extraData = array();

    public function __construct(Container $container) {
        $this->container = $container;
        $this->config = $container->get("config");
        $this->log = $container->get("log");
        $this->discordHelper = $container->get("discordHelper");
        $this->messages = $container->get("messages");
        $this->users = $container->get("users");
        $this->cleverbot = $container->get("cleverbot");

        $this->log->addInfo("Initializing Discord and Websocket connection...");
        // Got to use the StreamSelectLoop otherwise the library won't function
        $this->loop = new StreamSelectLoop();
        $this->discord = new Discord(array(
            "token" => $this->config->get("token", "discord"),
            "logger" => $this->log,
            "loop" => $this->loop
        ));
    }

    public function addPlugin($type, $command, $class, $perms, $description, $usage, $timer) {
        $this->log->addInfo("Adding plugin: {$command}");
        $this->$type[$command] = [
            'permissions' => $perms,
            'class' => $class,
            'description' => $description,
            'usage' => $usage,
            'timer' => $timer
        ];
    }

    public function run() {
        $this->discord->on("ready", function(Discord $discord) {
            // Update the presence status
            $game = $discord->factory(Game::class, array("name" => $this->config->get("game", "discord"), "url" => null, "type" => null));
            $discord->updatePresence($game, false);

            $guildCount = $this->discordHelper->getGuildCount($this->discord);
            $memberCount = $this->discordHelper->getMemberCount($this->discord);
            $this->log->addInfo("Currently available to: {$guildCount} guilds and {$memberCount} members...");
            $this->log->addInfo("Now using " . round(memory_get_usage() / 1024 / 1024, 2) . "MB memory (Peak: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB)...");

            // Setup the garbage collection timer
            $this->loop->addPeriodicTimer(300, function() {
                $this->log->addInfo("Collecting garbage...");
                gc_collect_cycles();

                $this->log->addInfo("Now using " . round(memory_get_usage() / 1024 / 1024, 2) . "MB memory (Peak: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB)...");
            });

            // Echo out how many guilds and members we're looking at
            $this->loop->addPeriodicTimer(300, function() {
                $guildCount = $this->discordHelper->getGuildCount($this->discord);
                $memberCount = $this->discordHelper->getMemberCount($this->discord);

                // Add in voice streams that are running
                $this->log->addInfo("Recount... Now available to: {$guildCount} guilds and {$memberCount} members...");
            });

            //@todo run a timer that checks if there is any active voice sessions. If there are, check if there are users listening, if not, end the session
        });

        // Message logging
        $this->discord->on(Event::MESSAGE_CREATE, function(Message $message, Discord $discord) {
            $this->log->addInfo("Message from: {$message->author}", array($message->content));

            // Log message to Mongo
            $channelData = $this->discordHelper->getChannel($discord, $message->channel_id);
            $guildData = $this->discordHelper->getGuild($discord, $channelData->guild_id);
            $userData = $this->discordHelper->getMember($discord, $message->author->id);
            $this->messages->storeMessage($message->content, (int) $message->id, $message->author->username, $userData->nick, (int) $message->author->id, $message->timestamp->toIso8601String(), $channelData->name, (int) $channelData->id, $guildData->name, (int) $guildData->id);

            // Add user to users table
            $this->users->storeUser($message->author->username, $userData->nick, (int) $message->author->id, (int) $userData->user->discriminator, $userData->user->avatar, $userData->status, $userData->game->name);
        });

        // Plugins
        $this->discord->on(Event::MESSAGE_CREATE, function(Message $message, Discord $discord) {
        });

        // Handle Voice
        $this->discord->on(Event::MESSAGE_CREATE, function(Message $message, Discord $discord) {
            $prefix = $this->config->get("prefix", "bot");
            if(substr($message->content, 0, strlen($prefix)) == $prefix) {
                $content = explode(" ", $message->content);
                foreach($this->onVoice as $command => $data) {
                    $parts = [];
                    foreach ($content as $index => $c) {
                        foreach (explode("\n", $c) as $p)
                            $parts[] = $p;
                    }
                    if ($parts[0] === $prefix . $command) {
                        try {
                            $channelData = $this->discordHelper->getChannel($discord, $message->channel_id);
                            $channels = $this->discordHelper->getChannelsForGuild($discord, $channelData->guild_id);
                            foreach($channels as $channel) {
                                if($channel->bitrate != null) {
                                    if(count($channel->members) > 0) {
                                        foreach($channel->members as $member) {
                                            if($member->user_id == $message->author->id) {
                                                $voice = new $data['class']();
                                                $voice->run($message, $discord, $this->log, $this->audioStreams, $channel, $channelData->guild_id, $this->container);
                                            }
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $this->log->addError("Error running voice command {$prefix}{$command}. Command run by {$message->author->username} in {$message->getChannelAttribute()->name}. Error: {$e->getMessage()}");
                            $message->reply("**Error:** There was a problem running the command: {$e->getMessage()}");
                        }
                    }
                }
            }
        });

        // Handle CleverBot
        $this->discord->on(Event::MESSAGE_CREATE, function(Message $message, Discord $discord) {
            if(stristr($message->content, $discord->id)) {
                $msg = str_replace("<@{$discord->id}>", "", $message->content);
                $channelData = $this->discordHelper->getChannel($discord, $message->channel_id);
                $cleverBotID = $this->cleverbot->createCleverBotForGuild($channelData->guild_id);

                if($cleverBotID != null) {
                    $msg = $this->cleverbot->getMessage($msg, $cleverBotID);
                    $message->getChannelAttribute()->broadcastTyping();
                    $message->reply($msg);
                }
            }
        });

        // Handle presence updates
        $this->discord->on(Event::PRESENCE_UPDATE, function(PresenceUpdate $presenceUpdate) {
            if(isset($presenceUpdate->user->id)) {
                $userData = $this->discordHelper->getMember($this->discord, $presenceUpdate->user->id);

                // Update a user
                $this->users->storeUser($userData->user->username, $userData->nick, (int)$presenceUpdate->user->id, (int)$userData->user->discriminator, $userData->user->avatar, $presenceUpdate->status, $presenceUpdate->game->name);
            }
        });

        // Handle guild joining/leaving
        $this->discord->on(Event::GUILD_CREATE, function(Guild $guild) {
        });

        $this->discord->on(Event::GUILD_DELETE, function(Guild $guild) {
        });

        // Handle errors
        $this->discord->on("error", function(\Exception $error, $websocket) {
            $this->log->addError("An error occured...", array($error->getMessage()));
            die();
        });
        $this->discord->on("close", function($opCode, $reason) {
            $this->log->addError("Connection was closed", array("code" => $opCode, "reason" => $reason));
            die();
        });
        $this->discord->on("reconnecting", function(){
            $this->log->addInfo("Reconnecting to server...");
        });
        $this->discord->on("reconnected", function(){
            $this->log->addInfo("Reconnect successfull...");
        });

        // Run the client
        $this->discord->run();
    }
}