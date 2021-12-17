<?php

/*
 * This file is part of blomstra/trello.
 *
 * Copyright (c) 2021 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Trello;

use Blomstra\Trello\Models\TrelloBoard;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\SettingsRepositoryInterface;

class TrelloAttributes
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(ForumSerializer $serializer, $model, array $attributes)
    {
        if ($serializer->getActor()->hasPermission('discussion.addToTrello')) {
            $attributes['trelloBoards'] = TrelloBoard::all()->toArray();
            $attributes['trelloDefaultBoardId'] = $this->settings->get('blomstra-trello.default_board_id');
            $attributes['trelloLastUsedLaneId'] = $this->settings->get('blomstra-trello.last_used_lane_id');
            $attributes['trelloLabelTagMappings'] = $this->settings->get('blomstra-trello.label-tag-mappings');
            $attributes['trelloUnmappedTagWarning'] = (bool) $this->settings->get('blomstra-trello.unmapped_tag_warning');
        }

        return $attributes;
    }
}
