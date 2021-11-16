<?php

namespace Blomstra\Trello\Controllers;

use Illuminate\Support\Arr;
use Blomstra\Trello\Models\TrelloBoard;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Api\Controller\AbstractDeleteController;

class DeleteBoardController extends AbstractDeleteController
{
    protected function delete(ServerRequestInterface $request)
    {
        $shortLink = Arr::get($request->getQueryParams(), 'shortLink');

        TrelloBoard::where('short_link', $shortLink)->delete();
    }
}
