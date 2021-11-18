<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Controllers;

use Illuminate\Support\Arr;
use Flarum\Http\RequestUtil;
use Blomstra\Trello\Models\TrelloBoard;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Api\Controller\AbstractDeleteController;

class DeleteBoardController extends AbstractDeleteController
{
    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $shortLink = Arr::get($request->getQueryParams(), 'shortLink');

        TrelloBoard::where('short_link', $shortLink)->delete();
    }
}
