<?php

namespace ayd1ndemirci;

use ayd1ndemirci\commands\TotemShopCommand;
use ayd1ndemirci\event\PlayerListener;
use ayd1ndemirci\provider\SQLiteDatabase;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    public static Main $main;
    public const PRICE = 1000;
    private SQLiteDatabase $database;

    public function onLoad(): void
    {
        self::$main = $this;
        $this->saveDefaultConfig();
        $this->database = new SQLiteDatabase();
    }
    protected function onEnable(): void
    {
        $this->getLogger()->info("TotemShop aktif - @ayd1ndemirci");
        $this->getServer()->getCommandMap()->register("totem", new TotemShopCommand());
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
    }

    protected function onDisable(): void
    {
        if ($dataConnector = $this->getDatabase()->getDataConnector()) $dataConnector->close();
    }

    /**
     * @return self
     */
    public static function getInstance() :self {
        return self::$main;
    }

    /**
     * @return SQLiteDatabase
     */
    public function getDatabase() :SQLiteDatabase {
        return $this->database;
    }

}