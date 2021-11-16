<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('trello_boards', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('short_link');
});
