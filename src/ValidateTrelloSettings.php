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

        if (self::areInvalidSettings([$apiKey, $apiToken, $memberId])) {
            return false;
        }
    }

    static private function isInvalidSetting(mixed $value)
    {
        return $value === null || $value === '';
    }

    static private function areInvalidSettings(array $values)
    {
        return array_filter($values, function ($value) {
            return self::isInvalidSetting($value);
        });
    }
}
