<?php

namespace Blomstra\Trello\Models;

use Flarum\Database\AbstractModel;

class TrelloBoard extends AbstractModel
{
    protected $fillable = ['name', 'short_link'];
}
