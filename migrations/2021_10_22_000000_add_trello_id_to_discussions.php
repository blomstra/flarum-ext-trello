<?php

use Flarum\Database\Migration;

return Migration::addColumns('discussions', [
    'trello_card_id' => ['integer', 'unsigned' => true, 'nullable' => true, 'default' => null]
]);
