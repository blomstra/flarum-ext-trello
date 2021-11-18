<?php

namespace Blomstra\Trello\Listener;

use Blomstra\Trello\ValidateTrelloSettings;
use Exception;
use Flarum\Discussion\Event\Saving;
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

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }
    
    public function handle(Saving $event) {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (array_key_exists('trello', $attributes)) {
            $event->actor->assertCan('addToTrello', $event->discussion);

            if (!ValidateTrelloSettings::Settings($this->settings)) {
                return;
            }

            try {
                $apiKey = $this->settings->get('blomstra-trello.api_key');
                $apiToken = $this->settings->get('blomstra-trello.api_token');
    
                $client = new Client($apiKey);
    
                $client->setAccessToken($apiToken);
    
                $originalPost = $event->discussion->posts->first();
    
                $card = new Card($client);
                $card->name = $event->discussion->title;
                $card->desc = $originalPost->content;
                $card->idList = $attributes['trello']['lane'];
                $card = $card->save();
    
                $event->discussion->trello_card_id = $card->shortLink;
                //$discussion->save();
            } catch (Exception $e) {
                $this->logger->error($e->getTraceAsString());
                throw new Exception("Failed to communicate with Trello.");
            }
        }
    }
}
