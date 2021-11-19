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

class TrelloLaneSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($lane)
    {
        return [
            'id'   => $lane->id,
            'name' => $lane->name,
        ];
    }

    public function getType($lane)
    {
        return 'trello-lane';
    }

    public function getId($lane)
    {
        return $lane->id;
    }
}
