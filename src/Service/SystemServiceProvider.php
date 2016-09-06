<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 18:37
 */

namespace Nazara\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nazara\Helper\DiscordHelper;
use Nazara\Lib\Config;
use Nazara\Lib\cURL;
use Nazara\Model\CleverBot;
use Nazara\Model\Messages;
use Nazara\Model\Users;

class SystemServiceProvider extends AbstractServiceProvider {
    protected $provides = array(
        "log",
        "mongo",
        "config",
        "curl",
        "startTime",
        "discordHelper",
        "messages",
        "users",
        "cleverbot"
    );

    public function register() {
        $container = $this->getContainer();

        $container->share("startTime", time());

        $config = new Config(__DIR__ . "/../../config/config.php");
        $container->share("config", $config);

        $container->share("log", "Monolog\\Logger")->withArgument("Nazara");
        $container->get("log")->pushHandler(new StreamHandler("php://stdout", Logger::INFO));

        $mongo = new Client("mongodb://localhost:27017", array(), array("typeMap" => array("root" => "array", "document" => "array", "array" => "array")));
        $container->share("mongo", $mongo);

        $container->share("curl", cURL::class);

        $container->share("discordHelper", DiscordHelper::class);
        $container->share("messages", Messages::class)->withArgument("config")->withArgument("mongo");
        $container->share("users", Users::class)->withArgument("config")->withArgument("mongo");
        $container->share("cleverbot", CleverBot::class)->withArgument("config")->withArgument("mongo")->withArgument("curl");
    }
}