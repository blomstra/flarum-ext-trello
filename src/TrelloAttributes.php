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

use Blomstra\Trello\Models\TrelloBoard;
use Flarum\Api\Serializer\ForumSerializer;

class TrelloAttributes
{
    public function __invoke(ForumSerializer $serializer, $model, array $attributes)
    {
        if ($serializer->getActor()->hasPermission('discussion.addToTrello')) {
            $attributes['trelloBoards'] = TrelloBoard::all()->toArray();
        }

        return $attributes;
    }
}
