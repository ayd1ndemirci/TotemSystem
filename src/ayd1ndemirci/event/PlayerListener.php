<?php

namespace ayd1ndemirci\event;

use ayd1ndemirci\event\custom\TotemUseEvent;
use ayd1ndemirci\Main;
use ayd1ndemirci\provider\SQLiteDatabase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\world\sound\TotemUseSound;
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
    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $totem = Main::getInstance()->getManager()->getPlayerTotem($player->getName());
        if ($totem > 0) {
            Main::getInstance()->getManager()->takePlayerTotem($player->getName(), 1);
            $player->sendMessage("§8» §cYou are dead but your belongings are protected because you have a totem.\n§cRemaining totem: §4" . $totem - 1);
            $player->getWorld()->addSound($player->getPosition(), new TotemUseSound(), [$player]);
            $event->setKeepInventory(true);
            $event->setKeepXp(true);
            (new TotemUseEvent($player))->call();
        }
    }
    public function totemUseEvent(TotemUseEvent $event) :void
    {
        $player = $event->getPlayer();
        $totem = $event->getTotem();
        Main::getInstance()->getManager()->takePlayerTotem($player->getName(), -1);
    }
}
