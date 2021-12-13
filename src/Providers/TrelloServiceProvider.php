<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Providers;

use Blomstra\Trello\ValidateTrelloSettings;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;
use Trello\Client as TrelloClient;

class TrelloServiceProvider extends AbstractServiceProvider
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function register()
    {
        $this->container->singleton('blomstra.trello.client', function ($container) {
            $settings = $container->make(SettingsRepositoryInterface::class);

            if (!ValidateTrelloSettings::Settings($settings)) {
                return null;
            }

            $apiKey = $settings->get('blomstra-trello.api_key');
            $apiToken = $settings->get('blomstra-trello.api_token');

            return (new TrelloClient($apiKey))->setAccessToken($apiToken);
        });

        $this->container->alias('blomstra.trello.client', TrelloClient::class);
    }
}
