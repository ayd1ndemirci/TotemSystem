<?php

namespace ayd1ndemirci\commands;

use ayd1ndemirci\form\TotemShopForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TotemShopCommand extends Command
{

    public function __construct()
    {
        parent::__construct("totem", "Totem shop");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args) :void
    {
        if ($sender instanceof Player) {
            $sender->sendForm(new TotemShopForm($sender));
        }
    }
}
