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

use Blomstra\Trello\Controllers\AddBoardController;
use Blomstra\Trello\Controllers\DeleteBoardController;
use Blomstra\Trello\Controllers\ListBoardLabelsController;
use Blomstra\Trello\Controllers\ListBoardLanesController;
use Blomstra\Trello\Controllers\ListBoardMembersController;
use Blomstra\Trello\Controllers\ListBoardsController;
use Blomstra\Trello\Providers\TrelloServiceProvider;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;

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
        ->get('/blomstra/trello/api-boards/{board}/lanes', 'blomstra::trello.boards-api.lanes.index', ListBoardLanesController::class)
        ->get('/blomstra/trello/api-boards/{board}/labels', 'blomstra::trello.boards-api.labels.index', ListBoardLabelsController::class)
        ->get('/blomstra/trello/api-boards/{board}/members', 'blomstra::trello.boards-api.members.index', ListBoardMembersController::class)
        ->post('/blomstra/trello/boards', 'blomstra::trello.boards.store', AddBoardController::class)
        ->delete('/blomstra/trello/boards/{shortLink}', 'blomstra::trello.boards.destroy', DeleteBoardController::class),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('trelloCardId', function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            return $discussion->trello_card_id;
        })
        ->attribute('canAddToTrello', function (DiscussionSerializer $serializer, Discussion $discussion, array $attributes) {
            return (bool) $serializer->getActor()->can('addToTrello', $discussion);
        }),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(TrelloAttributes::class),

    (new Extend\Event())
        ->listen(Saving::class, Listener\SaveTrelloIdToDatabase::class),

    (new Extend\ServiceProvider())
        ->register(TrelloServiceProvider::class),
];
