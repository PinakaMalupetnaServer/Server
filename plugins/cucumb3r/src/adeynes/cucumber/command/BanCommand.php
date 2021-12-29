<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\CommandParser;
use adeynes\parsecmd\command\ParsedCommand;
use InvalidArgumentException;
use pocketmine\command\CommandSender;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class BanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ban',
            'cucumber.command.ban',
            'Ban a player by name',
            '/ban <player> <duration>|inf [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $duration, $reason] = $command->get(['player', 'duration', 'reason']);
        $target_name = strtolower($target_name);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }
        if (in_array($duration, self::PERMANENT_DURATION_STRINGS)) {
            $expiration = null;
        } else {
            try {
                $expiration = $duration ? CommandParser::parseDuration($duration) : null;
            } catch (InvalidArgumentException $exception) {
                $this->getPlugin()->formatAndSend($sender, 'error.invalid-duration', ['duration' => $duration]);
                return false;
            }
        }

        $ban = function () use ($duration, $sender, $target_name, $reason, $expiration) {
            try {
                $ban = new Ban($target_name, $reason, $expiration, $sender->getName(), time());
                $ban_data = $ban->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addBan($ban);
                $ban->save($this->getPlugin()->getConnector());

                if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                    $target->kick(
                        $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data),
                        false // don't say Kicked by admin
                    );
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ban', $ban_data);

                // send details on discord server
                $whook = $this->getPlugin()->getConfig()->get('webh');
                $webhook = new Webhook($whook);

                $msg = new Message();
                $msg->setUsername("cucumBAN");
                $msg->setAvatarURL("https://th.bing.com/th/id/R.3e31457af0eba4508a0f69e2aa4415f8?rik=okgaal1d19EDsg&riu=http%3a%2f%2fpngimg.com%2fuploads%2fcucumber%2fcucumber_PNG84281.png&ehk=1SM1m9pziiqKralyNFy2tsj4Hp%2fBWelIZK8Y2BVqG5s%3d&risl=&pid=ImgRaw&r=0");
                $msg->setContent("");

                $embed = new Embed();
                $embed->setTitle("BANNED");
                $embed->setColor(0xFF0000);
                $embed->setDescription($target_name . " is banned by " . $sender->getName() . " for " . $duration . " due to " . $reason);
                $embed->setFooter("🥒 by princepines and adeynes");
                $msg->addEmbed($embed);

                $webhook->send($msg);

                return true;
            } catch(CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $this->doIfTargetExists($ban, $sender, $target_name);
        return true;
    }

}
