<?php

namespace ayd1ndemirci\event;

use ayd1ndemirci\Main;
use ayd1ndemirci\provider\SQLiteDatabase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use SOFe\AwaitGenerator\Await;

class PlayerListener implements Listener
{

    /*** @var SQLiteDatabase */
    private SQLiteDatabase $database;

    public function __construct() {
        $this->database = Main::getInstance()->getDatabase();
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event):void
    {
        $player = $event->getPlayer();
        Await::f2c(function () use($player) {
            $rows = (array) yield from $this->database->getPlayerToken($player->getName());
            if(empty($rows)) $this->database->addPlayer($player->getName());
        });
    }
    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onPlayerDeath(PlayerDeathEvent $event) :void {
        $event->setKeepInventory(true);
    }
}