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

use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Symfony\Contracts\Translation\TranslatorInterface;
use Trello\Client;
use Trello\Models\Board;
use Trello\Models\Card;
use Trello\Models\Label;

class SaveTrelloIdToDatabase
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, UrlGenerator $url, Client $client)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->url = $url;
        $this->client = $client;
    }

    public function handle(Saving $event)
    {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (array_key_exists('trello', $attributes)) {
            $discussion = $event->discussion;
            $event->actor->assertCan('addToTrello', $discussion);

            $card = $this->createTrelloCard($discussion, $attributes['trello']['lane']);

            if ($card) {
                $discussion->trello_card_id = $card->shortLink;

                $this->rememberLastUsedLaneId($attributes['trello']['lane']);
                $this->rememberLastUsedBoardId($attributes['trello']['board']['short_link']);

                $this->attachLabelsToCardBasedOnForumTags($discussion, $card, $attributes['trello']['board']['short_link']);
            }
        }
    }

    private function createTrelloCard(Discussion $discussion, string $trelloLane): ?Card
    {
        if ($this->client) {
            $card = new Card($this->client);
            $card->name = $discussion->title;
            $card->desc = $this->prefixContentWithUrl($discussion);
            $card->idList = $trelloLane;

            return $card->save();
        }

        return null;
    }

    private function prefixContentWithUrl(Discussion $discussion): string
    {
        $user = $discussion->user;

        $postUrl = $this->url->to('forum')->route('discussion', ['id' => $discussion->id]);
        $posterUrl = $this->url->to('forum')->route('user', ['username' => $user->username]);

        $originalPost = $this->translator->trans('blomstra-trello.lib.original_post');

        return "[{$originalPost}]($postUrl) - [{$user->username}]($posterUrl)\n\n".$discussion->posts->first()->content;
    }

    private function rememberLastUsedLaneId(string $lane): void
    {
        $currentSetting = $this->settings->get('blomstra-trello.last_used_lane_id');

        if (strcmp($currentSetting, $lane) !== 0) {
            $this->settings->set('blomstra-trello.last_used_lane_id', $lane);
        }
    }

    private function rememberLastUsedBoardId(string $shortLink): void
    {
        $currentSetting = $this->settings->get('blomstra-trello.default_board_id');

        if (strcmp($currentSetting, $shortLink) !== 0) {
            $this->settings->set('blomstra-trello.default_board_id', $shortLink);
        }
    }

    private function attachLabelsToCardBasedOnForumTags(Discussion $discussion, Card $card, string $shortLink)
    {
        if ($this->client) {
            $board = (new Board($this->client))->setId($shortLink)->get();

            if ($board) {
                $mappings = json_decode($this->settings->get('blomstra-trello.label-tag-mappings'), true);

                $boardMappings = Arr::get($mappings, $shortLink);

                if ($boardMappings) {
                    $discussion->tags->each(function ($tag) use ($boardMappings, $card) {
                        foreach ($boardMappings as $boardMapping) {
                            if ($boardMapping['tagId'] == $tag->id) {
                                $labelId = data_get($boardMapping, 'label.id');

                                $label = new Label($this->client);
                                $label = $label->setId($labelId)->get();

                                $card->addLabel($label);
                            }
                        }
                    });
                }
            }
        }
    }
}
