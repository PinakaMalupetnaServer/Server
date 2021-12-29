<?php

namespace Pmns\Selector;

use pocketmine\plugin\PluginBase as P;
use pocketmine\event\Listener as L;
use pocketmine\utils\TextFormat as TF;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent as PQ;
use pocketmine\event\player\PlayerInteractEvent as CL;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ListTag;

use jojoe77777\FormAPI\SimpleForm;

use pocketmine\item\Item;
use pocketmine\inventory\Inventory;

class Main extends P implements L
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        switch ($cmd->getName()) {
            case "games":
                if ($sender instanceof Player) {
                    $this->OpenUI($sender);
                }
                break;
        }
        return true;
    }

    public function onJoin(PlayerJoinEvent $EV)
    {
        $p = $EV->getPlayer();
        $pi = $p->getInventory();
        $pn = $p->getName();
        $name = $pn;
        $p->sendMessage(
            TF::BOLD . TF::GRAY . "------------ " . TF::RED . "PMnS" . TF::GRAY . " ------------\n" . TF::RESET .
            TF::GOLD . "Welcome! " . $name . "\n\n" .
            TF::DARK_AQUA . "Discord: https://discord.gg/wt5aH5Bujm\n" .
            TF::RED . "Website: https://pmns.princepines.digital\n" .
            TF::YELLOW . "Vote: https://pmns.princepines.digital/vote\n" .
            TF::BOLD . TF::GRAY . "------------ " . TF::RED . "PMnS" . TF::GRAY . " ------------");
        $EV->setJoinMessage(TF::GREEN . "+ " . $name);
        $level = $p->getLevel();
        $p->getInventory()->clearAll();
        $p->getArmorInventory()->clearAll();
        $compass = Item::get(Item::COMPASS);
        $compass->setCustomName(TF::RESET . TF::GOLD . "Game Selector" . TF::RESET);
            $compass->setLore([TF::RESET,
                TF::RESET . TF::GREEN . "Right Click/Interact to open Selector UI",
                TF::RESET]);
            $compass->getNamedTag()->setTag(new StringTag("game", "game"));
            $compass->setNamedTagEntry(new ListTag("ench", []));
            $pi->setItem(4, $compass);
	}

    public function onLeave(PQ $e)
    {
        $p = $e->getPlayer();
        $e->setQuitMessage(TF::RED . "- " . $p->getName());
    }

    public function click(CL $ev)
    {
        $player = $ev->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getNamedTag()->hasTag("game")) {
            $this->OpenUI($player);
        }
    }

    public function OpenUI($player)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {

                case 0:
                    $this->getServer()->dispatchCommand($player, "pvp");
                    break;

                case 1:
                    $this->getServer()->dispatchCommand($player, "survival");
                    break;

                case 2:
                    $player->sendMessage(TF::GRAY . "Coming Soon..");
                    break;
            }
            return true;
        });
        $form->setTitle(TF::BOLD . TF::RED . "GamesUI");
        $form->setContent("Please Select Game");
        $form->addButton(TF::BOLD . TF::GOLD ."Pot" . TF::WHITE . "PvP");
        $form->addButton(TF::BOLD . TF::GREEN . "Survival Mode");
        $form->addButton(TF::GRAY . "Coming Soon");

        $form->sendToPlayer($player);
        return $form;
    }
}
