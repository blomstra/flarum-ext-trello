import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Model from 'flarum/Model';
import Discussion from 'flarum/common/models/Discussion';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import SendToTrelloModal from '../components/SendToTrelloModal';
import Button from 'flarum/common/components/Button';
import LinkButton from 'flarum/common/components/LinkButton';

export default function () {
  Discussion.prototype.trelloCardId = Model.attribute('trelloCardId');
  Discussion.prototype.canAddToTrello = Model.attribute('canAddToTrello');

  extend(DiscussionPage.prototype, 'sidebarItems', function (items) {
    const discussion = this.discussion;

    if (!discussion.canAddToTrello() || !app.forum.attribute('trelloBoards')) {
      return;
    }

    const trelloId = discussion.trelloCardId();

    items.add(
      'blomstra-trello',
      trelloId ? (
        <LinkButton class="Button" icon="fab fa-trello" href={`https://trello.com/c/${trelloId}`} external={true} target="_blank">
          {app.translator.trans('blomstra-trello.forum.controls.view_on_trello_button')}
        </LinkButton>
      ) : (
        <Button icon="fab fa-trello" class="Button" onclick={() => app.modal.show(SendToTrelloModal, { discussion })}>
          {app.translator.trans('blomstra-trello.forum.controls.send_to_trello_button')}
        </Button>
      ), 75
    );
  });
}
