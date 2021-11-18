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

class TrelloBoardSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($organizationsBoard)
    {
        $result = [];
        foreach ($organizationsBoard as $organization => $boards) {
            $result[] = compact('organization', 'boards');
        }

        return $result;
    }

    public function getType($board)
    {
        return 'trello-board';
    }

    public function getId($board)
    {
        return null;
    }
}
