<?php

namespace Blomstra\Trello\Controllers;

use Tobscure\JsonApi\Document;
use Blomstra\Trello\Models\TrelloBoard;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractCreateController;
use Blomstra\Trello\Serializer\DatabaseBoardSerializer;

class AddBoardController extends AbstractCreateController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * {@inheritdoc}
     */
    public $serializer = DatabaseBoardSerializer::class;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $body = json_decode($request->getBody()->getContents())->selected;

        $data = [
            'name' => $body->text,
            'short_link' => $body->shortLink,
        ];

        if (TrelloBoard::where('short_link', $data['short_link'])->count() == 0) {
            return TrelloBoard::create($data);
        }
    }
}
