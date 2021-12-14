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

class TrelloMemberSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($member)
    {
        return [
            'id'        => $member->id,
            'fullName'  => $member->fullName,
            'username'  => $member->username,
        ];
    }

    public function getType($member)
    {
        return 'trello-member';
    }

    public function getId($member)
    {
        return $member->id;
    }
}
