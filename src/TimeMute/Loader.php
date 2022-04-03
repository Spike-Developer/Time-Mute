<?php

namespace TimeMute;

use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use TimeMute\task\Task;

class Loader extends PluginBase implements Listener
{

    public Config $config;

    /**
     * @throws JsonException
     */
    protected function onEnable(): void
    {
        mkdir($this->getDataFolder());
        $this->getScheduler()->scheduleRepeatingTask(new Task($this), 20);
        $this->config = new Config($this->getDataFolder() . "mutes.yml", Config::JSON);
        $this->config->save();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function chat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->get($player) > 0){
            $event->cancel();
        }
    }

    /**
     * @throws JsonException
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() == "timemute") {
            if ($sender->hasPermission("timemute.command")) {
                if (isset($args[0])) {
                    $player = $this->getServer()->getPlayerByPrefix($args[0]);
                    if ($player->isOnline()) {
                        if (!isset($args[1])) {
                            $sender->sendMessage(TextFormat::colorize("&c Use: /timemute [playerName] [time]"));
                        }
                        $this->set($player, $args[1]);
                        $sender->sendMessage(TextFormat::colorize("&a Mute {$player->getName()} for $args[1]"));
                    } else {
                        $sender->sendMessage(TextFormat::colorize("&c Player Offline"));
                    }
                } else {
                    $sender->sendMessage(TextFormat::colorize("&c Use: /timemute [playerName] [time]"));
                }
            } else {
                $sender->sendMessage(TextFormat::colorize("&c Not Permission"));
            }
        }
        return false;
    }

    /**
     * @throws JsonException
     */
    public function set(Player $player, int $time)
    {
        $this->config->set($player->getXuid(), $time);
        $this->config->save();
    }

    public function get(Player $player)
    {
        if ($this->config->exists($player->getXuid())){
            return $this->config->get($player->getXuid());
        }
        return 0;
    }

}
