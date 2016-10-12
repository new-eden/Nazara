<?php
namespace Nazara\Commands;

use Nazara\Helper\Container;
use Nazara\Nazara;
use Nazara\Service\SystemPluginServiceProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartBot extends Command {
    protected function configure() {
        $this->setName("start")
            ->setDescription("Start the bot")
            ->addOption("config", null, InputOption::VALUE_OPTIONAL, "The location of the configuration file...", __DIR__ . "/../../config/config.php");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        ini_set("memory_limit", "-1");
        gc_enable();
        error_reporting(1);
        error_reporting(E_ALL);

        // Load the container
        $container = (new Container())->getContainer();

        // Add the bot to the container
        $container->share("bot", Nazara::class)->withArgument($container);

        // Init the bot
        try {
            $container->get("log")->addInfo("Nazara is starting up...");
            $bot = $container->get("bot");

            // Add the plugins
            $container->addServiceProvider(SystemPluginServiceProvider::class);

            $bot->run();
        } catch(\Exception $e) {
            $container->get("log")->addError("Nazara could not start: {$e->getMessage()}");
            die();
        }
    }
}