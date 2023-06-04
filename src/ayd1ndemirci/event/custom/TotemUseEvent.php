<?php

namespace ayd1ndemirci\event\custom;

use ayd1ndemirci\Main;
use pocketmine\event\Event;
use pocketmine\player\Player;

class TotemUseEvent extends Event
{
    public Player $player;

    public int $totem;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->totem = Main::getInstance()->getManager()->getPlayerTotem($player->getName());
    }
    public function getPlayer() :Player
    {
        return $this->player;
    }
    public function getTotem() :int
    {
        return $this->totem;
    }
}