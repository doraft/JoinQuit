<?php

declare(strict_types=1);

namespace DOHWI\JoinQuit;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Symfony\Component\Filesystem\Path;
use function str_replace;

final class JoinQuit extends PluginBase
{
    private Config $config;

    private function initConfig(): void
    {
        $this->saveDefaultConfig();
        $lang = $this->getConfig()->get("language");
        $this->saveResource("$lang.json");
        $file = Path::join($this->getDataFolder(), "$lang.json");
        $this->config = new Config($file, Config::JSON);
    }

    protected function onEnable(): void
    {
        $this->initConfig();
        $this->getServer()->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event): void
        {
            $player = $event->getPlayer();
            $playerName = $player->getName();
            $event->setJoinMessage("");
            $message = str_replace("{name}", $playerName, $this->config->get("JOIN_TIP_MESSAGE"));
            $this->getServer()->broadcastTip($message);
        }, EventPriority::NORMAL, $this);

        $this->getServer()->getPluginManager()->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event): void
        {
            $playerName = $event->getPlayer()->getName();
            $event->setQuitMessage("");
            $message = str_replace("{name}", $playerName, $this->config->get("QUIT_TIP_MESSAGE"));
            $this->getServer()->broadcastTip($message);
        }, EventPriority::NORMAL, $this);
    }
}
