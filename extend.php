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

use Flarum\Extend;
use Flarum\Discussion\Discussion;
use Flarum\Api\Serializer\DiscussionSerializer;
use Blomstra\Trello\Controllers\AddBoardController;
use Blomstra\Trello\Controllers\ListBoardsController;
use Blomstra\Trello\Controllers\DeleteBoardController;
use Blomstra\Trello\Controllers\ListLanesBoardController;
use Blomstra\Trello\Controllers\UpdateDiscussionController;
use Blomstra\Trello\Controllers\ListDatabaseBoardController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->get('/blomstra/trello/api-boards', 'blomstra::trello.boards-api.index', ListBoardsController::class)
        ->get('/blomstra/trello/api-boards/{board}/lanes', 'blomstra::trello.boards-api.lanes.index', ListLanesBoardController::class)
        ->get('/blomstra/trello/boards', 'blomstra::trello.boards.index', ListDatabaseBoardController::class)
        ->post('/blomstra/trello/boards', 'blomstra::trello.boards.store', AddBoardController::class)
        ->delete('/blomstra/trello/boards/{shortLink}', 'blomstra::trello.boards.destroy', DeleteBoardController::class)
        ->patch('/blomstra/trello/discussions', 'blomstra::trello.discussions.update', UpdateDiscussionController::class),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('trelloCardId', function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            return $discussion->trello_card_id;
        })
        ->attribute('canAddToTrello', function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            return (bool) $serializer->getActor()->can('addToTrello', $discussion);
        }),
];
