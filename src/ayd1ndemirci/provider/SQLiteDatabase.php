<?php

namespace ayd1ndemirci\provider;

use ayd1ndemirci\Main;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use Closure;
use SOFe\AwaitGenerator\Await;

class SQLiteDatabase
{

    /*** @var DataConnector */
    private DataConnector $database;

    public function __construct() {
        $this->database = libasynql::create(
            Main::getInstance(),
            Main::getInstance()->getConfig()->get("database"),
            ["sqlite" => "database/sqlite.sql"]
        );
        $this->database->executeGeneric("totem.createTable");
    }

    /**
     * @param string $playerName
     * @return void
     */

    public function addPlayer(string $playerName):void
    {
      $this->database->executeInsert("totem.addPlayer", [
          "playerName" => $playerName
      ]);
    }

    /**
     * @param string $playerName
     * @param int $addedTokenCount
     * @param Closure $callbackFn
     * @return void
     */

    public function updateToken(string $playerName, int $addedTokenCount, Closure $callbackFn) :void {
        $this->database->executeChange("totem.updateTotem", [
            "playerName" => $playerName,
            "totemCount" => $addedTokenCount
        ], function() use($callbackFn) :void {
            $callbackFn();
        });
    }

    /**
     * @param string $playerName
     * @return \Generator
     */
    public function getPlayerToken(string $playerName) :\Generator {
        $this->database->executeSelect("totem.getTotem", [
            "playerName" => $playerName
        ], yield, yield Await::REJECT);
        return yield Await::ONCE;
    }

    /**
     * @return DataConnector
     */
    public function getDataConnector() :DataConnector {
        return $this->database;
    }
}