<?php

namespace BlockCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class one extends PluginBase implements Listener
{

    public $command;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->alert("BlockCommand Armed");
        $this->getLogger()->notice("Created by princepines, contributed by many.");
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");

    }


    public function onRun(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $inv = $player->getInventory();
        $armor = $player->getArmorInventory();
        $name = $player->getName();
        $level = $player->getLevel();
        $block = $player->getLevel()->getBlock($player->subtract(0, 1, 0));
        $this->myConfig = new Config($this->getDataFolder() . "config.yml", Config::YAML);


        // contains items and armors
        $fhotbarItem = Item::get(276, 0, 1); // usually swords
        $items = [$fhotbarItem, Item::get(346, 0, 1), Item::get(364, 0, 32), Item::get(368, 0, 16), Item::get(322, 0, 32), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1), Item::get(438, 21, 1)]; // the rest of items
        $setHelmet = Item::get(310, 0, 1);
        $setChestplate = Item::get(311, 0, 1);
        $setLeggings = Item::get(312, 0, 1);
        $setBoots = Item::get(313, 0, 1);

        // contains enchant arrays
        $fhotbarEnchant = [new EnchantmentInstance(Enchantment::getEnchantment(13), 2),
            new EnchantmentInstance(Enchantment::getEnchantment(9),3),
            new EnchantmentInstance(Enchantment::getEnchantment(17), 3)];
        $helmetEnchant = [new EnchantmentInstance(Enchantment::getEnchantment(17), 3),
            new EnchantmentInstance(Enchantment::getEnchantment(0), 2)];
        $chestEnchant = [new EnchantmentInstance(Enchantment::getEnchantment(9), 3),
            new EnchantmentInstance(Enchantment::getEnchantment(1), 2)];
        $leggingsEnchant = [new EnchantmentInstance(Enchantment::getEnchantment(9), 3)];
        $bootsEnchant = [new EnchantmentInstance(Enchantment::getEnchantment(9),3)];

        // contains foreach
        foreach ($fhotbarEnchant as $enchant) {
            $fhotbarItem->addEnchantment($enchant);
        }

        foreach ($helmetEnchant as $enchant) {
            $setHelmet->addEnchantment($enchant);
        }

        foreach ($chestEnchant as $enchant) {
            $setChestplate->addEnchantment($enchant);
        }

        foreach ($leggingsEnchant as $enchant) {
            $setLeggings->addEnchantment($enchant);
        }

        foreach ($bootsEnchant as $enchant) {
            $setBoots->addEnchantment($enchant);
        }

        if ($block->getId() === 0) return;
        if ($level->getName() === $this->myConfig->get('world')) {
            if ($block->getId() === $this->myConfig->get('block-id')) {
                if (empty($this->cooldown[$player->getName()])) {
                    $this->cooldown[$player->getName()] = time() + 20; // 20 is a second of cooldown
                    //$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "rca " . $name . " " ."kit kit");
                    $inv->clearAll();
                    foreach ($items as $item) {
                        $inv->addItem($item); // returns all items to player
                    }
                    $armor->setHelmet($setHelmet);
                    $armor->setChestplate($setChestplate);
                    $armor->setLeggings($setLeggings);
                    $armor->setBoots($setBoots);
                } else {
                    if (time() < $this->cooldown[$player->getName()]) {

                    } else {
                        unset($this->cooldown[$player->getName()]);
                    }
                }
            }
        }
    }

    public function onDisable()
    {
        $this->getLogger()->alert("BlockCommandir Disarmed");
        $this->getLogger()->notice("Created by princepines, contributed by many.");
    }
}
