<?php

namespace ImNotYourDev\Report\forms;

use ImNotYourDev\Report\libs\dktapps\pmforms\MenuForm;
use ImNotYourDev\Report\libs\dktapps\pmforms\MenuOption;
use ImNotYourDev\Report\Report;
use pocketmine\Player;
use pocketmine\Server;

class ReviewReportForm extends MenuForm
{
    public $report = [];

    public function __construct(array $report)
    {
        $this->report = $report;
        $title = "§6ReportSystem §7>§f Review";
        $text = "§7Reportname: §e" . $report["name"] . "\n§7Reporter: §e" . $report["reporter"] . "\n§7Reported player: §e" . $report["player"] . "\n§7Description: §e" . $report["desc"] . "\n§7Notes: §e" . $report["notes"] . "\n\n§7Choose now what u want to do";
        $options = [
            new MenuOption("§1Teleport(only if online)"),
            new MenuOption("§cBan player"),
            new MenuOption("§4Delete report"),
            new MenuOption("§aBack")
        ];

        Report::getInstance()->setReviewed($report["nestdir"]);

        parent::__construct($title, $text, $options, function (Player $player, $data)  : void {
            $target = Report::getInstance()->getServer()->getPlayer($this->report["player"]);
            if($data == 0){
                //$target = Report::getInstance()->getServer()->getPlayer($this->report["player"]);
                if($target == true){
                    $player->teleport($target->asPosition());
                    $player->sendMessage(Report::getInstance()->prefix . "You were teleported to §e" . $target->getName() . "§7!");
                }else{
                    $player->sendMessage(Report::getInstance()->prefix . "§cPlayer isn't online!");
                    $player->sendForm($this);
                }
            }elseif($data == 1){
                if($target->getServer()->getPluginManager()->getPlugin("cucumber")->getName() === "cucumber") {
                    $newCommand = str_replace("{player}", $target->getName(), "ban {player} inf Banned by ReportSystem");
                } else {
                    $newCommand = str_replace("{player}", $target->getName(), "ban {player} Banned by ReportSystem");
                }
                $player->getServer()->dispatchCommand($player, $newCommand);
                $player->sendMessage(Report::getInstance()->prefix . "§eBanned Sucessfully");
                $player->sendForm($this);
            }elseif($data == 2){
                Report::getInstance()->moveToRecycleBin($this->report);
                $player->sendMessage(Report::getInstance()->prefix . "Report moved to Recycle Bin!");
                $player->sendForm(new ReportListForm());
            }else{
                $player->sendForm(new ReportListForm());
            }
            return;
        });
    }
}