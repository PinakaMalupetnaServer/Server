<?php

namespace Hytlenz;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\{Player, Server};
use pocketmine\command\{Command, CommandSender};
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener{
  
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("Information has been revealed");
		
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
	}
	
	public function onDisable(){
		$this->getLogger()->info("Information has been leashed");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		switch($cmd->getName()){
			case "info":
				$this->infoForm($sender);
				break;
		}
		return true;
	}
	
	public function infoForm($sender){
		$form = new SimpleForm(function (Player $sender, $data) {
            		if (is_null($data)) return true;
            		$buttons = array_keys($this->cfg->get("wiki"));
            		if (count($buttons) == $data) return;
            		$button = $buttons[$data];
			$this->pageForm($sender, $button);
        	});
        	$form->setTitle($this->cfg->getNested("wikipedia.title"));
        	$form->setContent(implode("\n", str_replace("{player}", $sender->getName(), $this->cfg->getNested("wikipedia.content"))));
        	foreach(array_keys($this->cfg->get("wiki")) as $wiki) {
            		$form->addButton(
				$this->cfg->getNested("wiki." . $wiki . ".button"),
				$this->cfg->getNested("wiki." . $wiki . ".imgtype"), 
				$this->cfg->getNested("wiki." . $wiki . ".imgpath")
			);
        	}
        	$form->sendToPlayer($sender);
	}

	public function pageForm($sender, $button){
		$form = new SimpleForm(function (Player $sender, $data) {
            		if (is_null($data)) return true;
            		switch ($data) {
                		case 0:
					$this->infoForm($sender);
				break;
            		}
        	});
        	$form->setTitle($this->cfg->getNested("wiki." . $button . ".title"));
        	$form->setContent(implode("\n", $this->cfg->getNested("wiki." . $button . ".content")));
        	$form->addButton($this->cfg->getNested("wikipedia.return"));
        	$form->sendToPlayer($sender);
	}
	
}
