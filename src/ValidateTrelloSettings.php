<?php

namespace Blomstra\Trello;

use Flarum\Settings\SettingsRepositoryInterface;

class ValidateTrelloSettings
{
    static function Settings(SettingsRepositoryInterface $settings)
    {
        $apiKey = $settings->get('blomstra-trello.api_key');
        $apiToken = $settings->get('blomstra-trello.api_token');
        $memberId = $settings->get('blomstra-trello.member_id');

        return !self::areInvalidSettings([$apiKey, $apiToken, $memberId]);
    }

    static private function isInvalidSetting(mixed $value)
    {
        return $value === null || $value === '';
    }

    static private function areInvalidSettings(array $values)
    {
        foreach ($values as $value) {
            if (self::isInvalidSetting($value)) {
                return true;
            }
        };

        return false;
    }
}
