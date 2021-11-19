<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello;

use Flarum\Settings\SettingsRepositoryInterface;

class ValidateTrelloSettings
{
    public static function Settings(SettingsRepositoryInterface $settings)
    {
        $apiKey = $settings->get('blomstra-trello.api_key');
        $apiToken = $settings->get('blomstra-trello.api_token');
        $memberId = $settings->get('blomstra-trello.member_id');

        return !self::areInvalidSettings([$apiKey, $apiToken, $memberId]);
    }

    private static function isInvalidSetting(mixed $value)
    {
        return $value === null || $value === '';
    }

    private static function areInvalidSettings(array $values)
    {
        foreach ($values as $value) {
            if (self::isInvalidSetting($value)) {
                return true;
            }
        }

        return false;
    }
}
