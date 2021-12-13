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

use Blomstra\Trello\Serializer\TrelloMemberSerializer;
use Exception;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Trello\Client as TrelloClient;
use Trello\Models\Board;

class ListBoardMembersController extends AbstractListController
{
    /**
     * @var TrelloClient
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public $serializer = TrelloMemberSerializer::class;

    public function __construct(TrelloClient $client)
    {
        $this->client = $client;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        if (!$this->client) {
            return [];
        }

        try {
            $board = Arr::get($request->getQueryParams(), 'board');

            $board = (new Board($this->client))->setId($board)->get();

            return $board->getMembers();
        } catch (Exception $e) {
        }

        return [];
    }
}
