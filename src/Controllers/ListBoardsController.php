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

use Blomstra\Trello\Serializer\TrelloBoardSerializer;
use Blomstra\Trello\ValidateTrelloSettings;
use Exception;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tobscure\JsonApi\Document;
use Trello\Client as TrelloClient;
use Trello\Models\Member;

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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var TrelloClient
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public $serializer = TrelloBoardSerializer::class;

    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, LoggerInterface $logger, TrelloClient $client)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->client = $client;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        if (!ValidateTrelloSettings::Settings($this->settings)) {
            return [];
        }

        $selection = [];

        try {
            $memberId = $this->settings->get('blomstra-trello.member_id');

            $member = new Member($this->client);
            $member->setId($memberId);

            $organizations = collect($member->getOrganizations())->pluck('displayName', 'id')->toArray();

            $boards = $member->getBoards();

            foreach ($boards as $board) {
                $data = ['name' => $board->name, 'short_link' => $board->shortLink];

                if (isset($organizations[$board->idOrganization])) {
                    $selection[$organizations[$board->idOrganization]][] = $data;

                    continue;
                }

                $selection[$this->translator->trans('blomstra-trello.admin.settings.guest_workspace')][] = $data;
            }
        } catch (Exception $e) {
            $this->logger->error($e->getTraceAsString());

            throw new Exception('Failed to communicate with Trello.');
        }

        return $selection;
    }
}
