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

use Blomstra\Trello\Serializer\TrelloLabelSerializer;
use Blomstra\Trello\ValidateTrelloSettings;
use Exception;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Trello\Client;
use Trello\Models\Board;

class ListLabelsBoardController extends AbstractListController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * {@inheritdoc}
     */
    public $serializer = TrelloLabelSerializer::class;

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

            return $board->getLabels();
        } catch (Exception $e) {
        }

        return [];
    }
}
