<?php

namespace Skin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\UUID;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\PersonaPieceTintColor;
use pocketmine\network\mcpe\protocol\types\PersonaSkinPiece;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\types\SkinAnimation;
use pocketmine\network\mcpe\protocol\types\SkinData;
use pocketmine\network\mcpe\protocol\types\SkinImage;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0744, true); 
		}
		if(!file_exists($this->getDataFolder() . "player/")){
			mkdir($this->getDataFolder() . "player/" , 0744, true); 
		}
	}

	public function onReceive(DataPacketReceiveEvent $event){
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		$name = $player->getName();
		if($packet instanceof LoginPacket){

			$animations = [];
			foreach($packet->clientData["AnimatedImageData"] as $animation){
				$animations[] = new SkinAnimation(
					new SkinImage(
						$animation["ImageHeight"],
						$animation["ImageWidth"],
						base64_decode($animation["Image"], true)),
					$animation["Type"],
					$animation["Frames"],
					$animation["AnimationExpression"]
				);
			}
	
			$personaPieces = [];
			foreach($packet->clientData["PersonaPieces"] as $piece){
				$personaPieces[] = new PersonaSkinPiece(
					$piece["PieceId"],
					$piece["PieceType"],
					$piece["PackId"],
					$piece["IsDefault"],
					$piece["ProductId"]
				);
			}
	
			$pieceTintColors = [];
			foreach($packet->clientData["PieceTintColors"] as $tintColor){
				$pieceTintColors[] = new PersonaPieceTintColor($tintColor["PieceType"], $tintColor["Colors"]);
			}

			$skinData = new SkinData(
				$packet->clientData["SkinId"],
				$packet->clientData["PlayFabId"],
				base64_decode($packet->clientData["SkinResourcePatch"] ?? "", true),
				new SkinImage(
					$packet->clientData["SkinImageHeight"],
					$packet->clientData["SkinImageWidth"],
					base64_decode($packet->clientData["SkinData"], true)
				),
				$animations,
				new SkinImage(
					$packet->clientData["CapeImageHeight"],
					$packet->clientData["CapeImageWidth"],
					base64_decode($packet->clientData["CapeData"] ?? "", true)
				),
				base64_decode($packet->clientData["SkinGeometryData"] ?? "", true),
				base64_decode($packet->clientData["SkinGeometryDataEngineVersion"] ?? 0, true),
				base64_decode($packet->clientData["SkinAnimationData"] ?? "", true),
				$packet->clientData["CapeId"] ?? "",
				null,
				$packet->clientData["ArmSize"] ?? SkinData::ARM_SIZE_WIDE,
				$packet->clientData["SkinColor"] ?? "",
				$personaPieces,
				$pieceTintColors,
				true,
				$packet->clientData["PremiumSkin"] ?? false,
				$packet->clientData["PersonaSkin"] ?? false,
				$packet->clientData["CapeOnClassicSkin"] ?? false,
				true, //assume this is true? there's no field for it ...
			);

			$serialized_skinData = serialize($skinData);
			file_put_contents($this->getDataFolder() . "player/".$packet->username.".ser", $serialized_skinData);
		}
	}
}
