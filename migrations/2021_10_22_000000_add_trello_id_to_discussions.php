<?php

use Flarum\Database\Migration;

return Migration::addColumns('discussions', [
    'trello_card_id' => ['string', 'length' => 16, 'nullable' => true, 'default' => null],
]);
