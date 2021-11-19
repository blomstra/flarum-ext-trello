<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Database\Migration;

return Migration::addColumns('discussions', [
    'trello_card_id' => ['string', 'length' => 16, 'nullable' => true, 'default' => null],
]);
