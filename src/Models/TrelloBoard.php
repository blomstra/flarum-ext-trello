<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Models;

use Flarum\Database\AbstractModel;

class TrelloBoard extends AbstractModel
{
    protected $fillable = ['name', 'short_link'];
}
