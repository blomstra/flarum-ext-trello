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

use Blomstra\Trello\Serializer\TrelloLaneSerializer;
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

            $client = resolve(Client::class);

            $board = (new Board($client))->setId($board)->get();

            return $board->getLists();
        } catch (Exception $e) {
        }

        return [];
    }
}
