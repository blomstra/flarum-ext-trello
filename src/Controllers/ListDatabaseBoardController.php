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

use Flarum\Http\RequestUtil;
use Tobscure\JsonApi\Document;
use Blomstra\Trello\Models\TrelloBoard;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractListController;
use Blomstra\Trello\Serializer\DatabaseBoardSerializer;

class ListDatabaseBoardController extends AbstractListController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * {@inheritdoc}
     */
    public $serializer = DatabaseBoardSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        return TrelloBoard::get();
    }
}
