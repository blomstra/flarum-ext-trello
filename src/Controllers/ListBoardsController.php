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
use Trello\Model\Member;
use Flarum\Http\RequestUtil;
use Tobscure\JsonApi\Document;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Api\Controller\AbstractShowController;
use Blomstra\Trello\Serializer\TrelloBoardSerializer;
use Blomstra\Trello\ValidateTrelloSettings;
use Psr\Log\LoggerInterface;

class ListBoardsController extends AbstractShowController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * {@inheritdoc}
     */
    public $serializer = TrelloBoardSerializer::class;

    public function __construct(SettingsRepositoryInterface $settings, LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        if (!ValidateTrelloSettings::Settings($this->settings)) {
            return [];
        }

        $selection = [];

        try {
            $apiKey = $this->settings->get('blomstra-trello.api_key');
            $apiToken = $this->settings->get('blomstra-trello.api_token');
            $memberId = $this->settings->get('blomstra-trello.member_id');

            $client = new Client($apiKey);

            $client->setAccessToken($apiToken);

            $member = new Member($client);
            $member->setId($memberId);

            $organizations = collect($member->getOrganizations())->pluck('displayName', 'id')->toArray();

            $boards = $member->getBoards();

            foreach ($boards as $board) {
                $data = ['name' => $board->name, 'short_link' => $board->shortLink];

                if (isset($organizations[$board->idOrganization])) {
                    $selection[$organizations[$board->idOrganization]][] = $data;

                    continue;
                }

                $selection['Guest Workspace'][] = $data;
            }
        } catch (Exception $e) {
            $this->logger->error($e->getTraceAsString());
            throw new Exception("Failed to communicate with Trello.");
        }

        return $selection;
    }
}
