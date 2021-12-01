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

class TrelloLabelSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($label)
    {
        return [
            'id'   => $label->id,
            'name' => $label->name,
            'color' => $label->color,
        ];
    }

    public function getType($label)
    {
        return 'trello-label';
    }

    public function getId($label)
    {
        return $label->id;
    }
}
