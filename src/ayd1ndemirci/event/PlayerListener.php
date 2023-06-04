<?php

namespace ayd1ndemirci\event;

use ayd1ndemirci\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class PlayerListener implements Listener
{
    function onJoin(PlayerJoinEvent $event):void
    {
        $player = $event->getPlayer();
        Main::getInstance()->getDatabase()->getPlayerToken($player->getName(), function ($token) use ($player) {
            if (empty($token)) {
                Main::getInstance()->getDatabase()->addPlayer($player->getName());
            }
        });
    }
}