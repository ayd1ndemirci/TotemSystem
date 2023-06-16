    <?php

namespace ayd1ndemirci\form;

use ayd1ndemirci\Main;
use ayd1ndemirci\provider\SQLiteDatabase;
use pocketmine\form\Form;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use onebone\economyapi\EconomyAPI;
use SOFe\AwaitGenerator\Await;

class TotemShopForm implements Form
{

    /*** @var Player $sender */
    private Player $player;

    /*** @var SQLiteDatabase $database */
    private SQLiteDatabase $database;

    /*** @param Player $player */
    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->database = Main::getInstance()->getDatabase();
    }

    /*** @return array */
    public function jsonSerialize(): array
    {
        $totem = 0;
        Await::f2c(function () use (&$totem) {
            $rows = (array)yield from $this->database->getPlayerToken($this->player->getName());
            $totem = $rows[0]["totemCount"];
        });
        $this->database->getDataConnector()->waitAll();

        return [
            "type" => "custom_form",
            "title" => "Totem",
            "content" => [
                ["type" => "label", "text" => "\n§7» §fTotem: §e" . $totem],
                ["type" => "input", "text" => "\nAmount", "placeholder" => "Örn.; 1"],
                ["type" => "label", "text" => "\n§8» §c§oNote: Price per totem §4" . Main::PRICE . " §c$\n"]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if (is_null($data)) return;
        $amount = $data[1];
        if (empty($amount)) {
            $player->sendMessage("§8» §cThe quantity part cannot be empty.");
            return;
        }
        if ($amount < 1) {
            $player->sendMessage("§8» §cEnter 1 and numbers greater than 1.");
            return;
        }
        $price = $amount * Main::PRICE;
        if (EconomyAPI::getInstance()->myMoney($player) >= $price) {
            EconomyAPI::getInstance()->reduceMoney($player, $price);
            Main::getInstance()->getManager()->addPlayerTotem($player->getName(), $amount);
            $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("note.pling", $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), 2.0, 2.0));
            $player->sendMessage("§8» §aYou have successfully purchased §2§o{$amount} §r§a totems.");
        } else $player->sendMessage("§8» §cNot enough money.");
    }
}
