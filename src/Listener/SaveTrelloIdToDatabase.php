<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello\Listener;

use Blomstra\Trello\ValidateTrelloSettings;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Trello\Client;
use Trello\Model\Card;

class SaveTrelloIdToDatabase
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    protected $url;

    public function __construct(SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->settings = $settings;
        $this->url = $url;
    }
    
    public function handle(Saving $event) {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (array_key_exists('trello', $attributes)) {
            $discussion = $event->discussion;
            $event->actor->assertCan('addToTrello', $discussion);

            $card = $this->createTrelloCard($discussion, $attributes['trello']['lane']);

            if ($card) {
                $discussion->trello_card_id = $card->shortLink;
            }
        }
    }

    private function createTrelloCard(Discussion $discussion, string $trelloLane): ?Card
    {
        if (!ValidateTrelloSettings::Settings($this->settings)) {
            return null;
        }
        
        $apiKey = $this->settings->get('blomstra-trello.api_key');
        $apiToken = $this->settings->get('blomstra-trello.api_token');

        $client = new Client($apiKey);

        $client->setAccessToken($apiToken);


        $card = new Card($client);
        $card->name = $discussion->title;
        $card->desc = $this->prefixContentWithUrl($discussion);
        $card->idList = $trelloLane;
        return $card->save();
    }

    private function prefixContentWithUrl(Discussion $discussion): string
    {
        $url = $this->url->to('forum')->route('discussion', ['id' => $discussion->id]);

        return "[Original post]($url)\n\n" . $discussion->posts->first()->content;
    }
}
