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

use Blomstra\Trello\Models\TrelloBoard;
use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteBoardController extends AbstractDeleteController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $shortLink = Arr::get($request->getQueryParams(), 'shortLink');

        TrelloBoard::where('short_link', $shortLink)->delete();

        $defaultBoardId = $this->settings->get('blomstra-trello.default_board_id');

        if ($shortLink == $defaultBoardId) {
            $this->settings->delete('blomstra-trello.default_board_id');
            $this->settings->delete('blomstra-trello.last_used_lane_id');
        }
    }
}
