<?php
namespace PHPSTORM_META {
    $STATIC_METHOD_TYPES = [
        \Interop\Container\ContainerInterface::get('') => [
            "log" instanceof \Monolog\Logger,
            "mongo" instanceof \MongoDB\Client,
            "config" instanceof \Nazara\Lib\Config,
            "curl" instanceof \Nazara\Lib\cURL,
            "bot" instanceof \Nazara\Nazara,
            "messages" instanceof \Nazara\Model\Messages,
            "discordHelper" instanceof \Nazara\Helper\DiscordHelper,
            "users" instanceof \Nazara\Model\Users,
            "cleverbot" instanceof \Nazara\Model\CleverBot,
            "startTime",
        ]
    ];
}