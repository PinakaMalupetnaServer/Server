<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\utils\Persistent;
use adeynes\cucumber\utils\Queries;
use poggit\libasynql\DataConnector;

class Warning extends PlayerPunishment implements Persistent
{

    use Expirable;

    public static function from(array $row): Warning
    {
        return new Warning($row['name'], $row['reason'], $row['expiration'], $row['moderator'], $row['time_created']);
    }

    public function __construct(string $player, string $reason, ?int $expiration, string $moderator, int $time_created)
    {
        $this->expiration = $expiration;
        parent::__construct($player, $reason, $moderator, $time_created);
    }

    public function getRawData(): array
    {
        return parent::getRawData() + ['expiration' => $this->getExpiration()];
    }

    public function getFormatData(): array
    {
        return [
            'player' => $this->getPlayer(),
            'reason' => $this->getReason(),
            'expiration' => $this->getExpirationFormatted(),
            'expired' => $this->getExpiredFormatted(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

    public function getMessagesPath(): string
    {
        return 'success.warnings.list';
    }

    public function save(DataConnector $connector, ?callable $onSuccess = null): void
    {
        $connector->executeInsert(
            Queries::CUCUMBER_PUNISH_WARN,
            $this->getRawData(),
            $onSuccess
        );
    }

}