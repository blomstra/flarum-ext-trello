<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Controllers;

use Exception;
use Trello\Client;
use Trello\Model\Board;
use Illuminate\Support\Arr;
use Flarum\Http\RequestUtil;
use Tobscure\JsonApi\Document;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractListController;
use Blomstra\Trello\Serializer\TrelloLaneSerializer;
use Blomstra\Trello\ValidateTrelloSettings;

class ListLanesBoardController extends AbstractListController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * {@inheritdoc}
     */
    public $serializer = TrelloLaneSerializer::class;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        if (!ValidateTrelloSettings::Settings($this->settings)) {
            return [];
        }

        try {
            $board = Arr::get($request->getQueryParams(), 'board');

            $apiKey = $this->settings->get('blomstra-trello.api_key');
            $apiToken = $this->settings->get('blomstra-trello.api_token');

            $client = new Client($apiKey);

            $client->setAccessToken($apiToken);

            $board = (new Board($client))->setId($board)->get();

            return $board->getLists();
        } catch (Exception $e) {
        }

        return [];
    }
}
