<?php

namespace ayd1ndemirci\form;

use ayd1ndemirci\Main;
use ayd1ndemirci\manager\Manager;
use pocketmine\form\Form;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use onebone\economyapi\EconomyAPI;
use pocketmine\world\sound\TotemUseSound;

class TotemShopForm implements Form
{

    /**
     * @param Player $sender
     */
    public $totem;

    public function __construct(\pocketmine\player\Player $sender)
    {
       $this->sender = $sender;
    }

    public function jsonSerialize():mixed
    {
        return [
            "type" => "custom_form",
            "title" => "Totem",
            "content" => [
                ["type" => "label", "text" => "\n§7» §fTotem: §e" ],
                ["type" => "input", "text" => "\nMiktar", "placeholder" => "Örn.; 1"],
                ["type" => "label", "text" => "\n§8» §c§oNot: Totem başına fiyat §4" . Main::PRICE . " §cTL'dir\n"]
            ]
        ];
    }
    public function handleResponse(Player $player, $data): void
    {
        if (is_null($data)) return;
        $amount = $data[1];
        if (empty($amount)) {
            $player->sendMessage("§8» §cMiktar kısmı boş olamaz.");
            return;
        }
        if ($amount < 1) {
            $player->sendMessage("§8» §c1 ve 1'den büyük sayılar gir.");
            return;
        }
        $price = $amount * Main::PRICE;
        if (EconomyAPI::getInstance()->myMoney($player) >= $price) {

            Main::getInstance()->getDatabase()->updateToken($player->getName(), $amount, function () use ($player, $price, $amount) {
                EconomyAPI::getInstance()->reduceMoney($player, $price);
                $player->sendMessage("§8» §aBaşarıyla §2{$amount} §aadet totem satın aldın.");
                Main::getInstance()->getDatabase()->getPlayerToken($player->getName(), fn($token) => var_dump("Token: ".$token));
                $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("note.pling", $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), 2.0, 2.0));
            });
        } else $player->sendMessage("§8» §cYetersiz para.");
    }
}