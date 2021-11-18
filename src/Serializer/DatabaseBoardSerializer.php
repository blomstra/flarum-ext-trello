<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class DatabaseBoardSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($board)
    {
        $attributes = [
            'name' => $board->name,
            'shortLink' => $board->short_link,
        ];

        return $attributes;
    }

    public function getType($board)
    {
        return 'database-board';
    }

    public function getId($board)
    {
        return $board->id;
    }
}
