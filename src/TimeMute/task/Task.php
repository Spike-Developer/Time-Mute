<?php

namespace TimeMute\task;

use JsonException;
use TimeMute\Loader;

class Task extends \pocketmine\scheduler\Task
{

    public Loader $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @throws JsonException
     */
    public function onRun(): void
    {
        $players = $this->plugin->config->getAll();
        foreach ($players as $player => $data){
            if ($this->plugin->config->get($player) === 0) continue;
            $time = $this->plugin->config->get($player);
            $this->plugin->config->set($player, $time - 1);
            $this->plugin->config->save();
        }
    }

}