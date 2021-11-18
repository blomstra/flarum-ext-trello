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

    if (!discussion.canAddToTrello() || app.forum.attribute('trelloBoards').length == 0) {
      return;
    }

    const trelloId = discussion.trelloCardId();
    const faIcon = 'fab fa-trello';

    items.add(
      'blomstra-trello',
      trelloId ? (
        <LinkButton className="Button" icon="fab fa-trello" href={`https://trello.com/c/${trelloId}`} external={true} target="_blank">
          {app.translator.trans('blomstra-trello.forum.controls.view_on_trello_button')}
        </LinkButton>
      ) : (
        Button.component(
          {
            icon: faIcon,
            className: 'Button',
            onclick: () => app.modal.show(SendToTrelloModal, { discussion }),
          },
          app.translator.trans('blomstra-trello.forum.controls.send_to_trello_button')
        )
      )
    );
  });
}
