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

use Exception;
use Trello\Client;
use Trello\Model\Card;
use Flarum\Http\RequestUtil;
use Tobscure\JsonApi\Document;
use Flarum\Discussion\Discussion;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractShowController;

class UpdateDiscussionController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = DiscussionSerializer::class;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $body = json_decode($request->getBody()->getContents());

        $discussion = Discussion::find($body->discussion);

        // TODO: do this to other controllers used in forum
        RequestUtil::getActor($request)->assertCan('addToTrello', $discussion);

        try {
            $apiKey = $this->settings->get('blomstra-trello.api_key');
            $apiToken = $this->settings->get('blomstra-trello.api_token');

            $client = new Client($apiKey);

            $client->setAccessToken($apiToken);

            $originalPost = $discussion->posts->first();

            $card = new Card($client);
            $card->name = $discussion->title;
            $card->desc = $originalPost->content;
            $card->idList = $body->selected->lane;
            $card = $card->save();

            $discussion->trello_card_id = $card->shortLink;
            $discussion->save();
        } catch (Exception $e) {
        }

        return $discussion;
    }
}
