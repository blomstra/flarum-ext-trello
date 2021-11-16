<?php

namespace Blomstra\Trello\Controllers;

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
        return TrelloBoard::get();
    }
}
