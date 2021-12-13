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

use Blomstra\Trello\Models\TrelloBoard;
use Blomstra\Trello\Serializer\DatabaseBoardSerializer;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class AddBoardController extends AbstractCreateController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = DatabaseBoardSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $body = json_decode($request->getBody()->getContents())->selected;

        $data = [
            'name'       => $body->text,
            'short_link' => $body->shortLink,
        ];

        if (TrelloBoard::where('short_link', $data['short_link'])->count() == 0) {
            return TrelloBoard::create($data);
        }
    }
}
