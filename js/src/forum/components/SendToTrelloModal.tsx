import app from 'flarum/forum/app';
import icon from 'flarum/common/helpers/icon';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export interface DatabaseBoard {
  name: string;
  shortLink: string;
}
export interface BoardLane {
  name: string;
  id: string;
}

interface IState {
  loading: boolean;
  boards: DatabaseBoard[] | null;
  lanes: BoardLane[] | null;
}

export default class SendToTrelloModal extends Modal {
  states: IState = {
    loading: false,
    boards: null,
    lanes: null,
  };

  title() {
    return app.translator.trans('blomstra-trello.forum.modals.title');
  }

  content() {
    return (
      <div class="Modal-body">
        <form class="Form">
          <div class="Form-group">
            <label>{app.translator.trans('blomstra-trello.forum.modals.fields.board')}</label>
            {this.states.boards ? (
              <span class="Select">
                <select
                  class="Select-input FormControl"
                  onchange={(e) =>
                    this.loadTrelloLanes({
                      shortLink: e.currentTarget.value,
                      text: e.currentTarget.selectedOptions[0].textContent,
                    })
                  }
                >
                  {this.states.boards.map((item) => {
                    return [<option value={item.shortLink()}>{item.name()}</option>];
                  })}
                </select>
                {icon('fas fa-sort Select-caret')}
              </span>
            ) : (
              <p>{app.translator.trans('blomstra-trello.admin.settings.mapping.no_boards')}</p>
            )}
          </div>
          <div class="Form-group">
            <label>{app.translator.trans('blomstra-trello.forum.modals.fields.lane')}</label>
            {this.states.lanes ? (
              <span class="Select">
                <select
                  class="Select-input FormControl"
                  onchange={(e) =>
                    this.setCurrentSelectedLane({
                      id: e.currentTarget.value,
                    })
                  }
                >
                  {this.states.lanes.map((item) => {
                    return [<option value={item.attributes.id}>{item.attributes.name}</option>];
                  })}
                </select>
                {icon('fas fa-sort Select-caret')}
              </span>
            ) : (
              <p>{app.translator.trans('blomstra-trello.forum.modals.no_available_boards_label')}</p>
            )}
          </div>
          <div class="Form-group">
            <Button className="Button Button--primary" type="submit" loading={this.loading} disabled={this.disabled}>
              {app.translator.trans('blomstra-trello.forum.controls.send_to_trello_button')}
            </Button>
          </div>
        </form>
      </div>
    );
  }

  oninit(vnode) {
    super.oninit(vnode);

    this.disabled = true;

    this.selected = {
      board: null,
      lane: null,
    };

    this.loadData();
  }

  async loadData() {
    this.loading = true;

    this.states.boards = await app.store.find('blomstra/trello/boards');

    if (this.states.boards) {
      const item = {
        shortLink: this.states.boards[0].shortLink(),
        text: this.states.boards[0].name(),
      };

      this.loadTrelloLanes(item);
    }

    m.redraw();

    this.loading = false;
  }

  setCurrentSelectedBoard(item) {
    this.selected.board = {
      shortLink: item.shortLink,
      text: item.text,
    };
  }

  setCurrentSelectedLane(item) {
    this.selected.lane = item.id;
  }

  async loadTrelloLanes(item) {
    this.states.lanes = null;

    this.disabled = true;

    this.setCurrentSelectedBoard(item);

    // load lanes based on the currently selected board
    const response = await app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + `/blomstra/trello/api-boards/${this.selected.board.shortLink}/lanes`,
    });
    const data = response.data;
    this.states.lanes = data;
    if (this.states.lanes) {
      this.setCurrentSelectedLane({ id: this.states.lanes[0].id });
    }
    this.disabled = false;

    m.redraw();
  }

  async onsubmit(e) {
    e.preventDefault();

    const selected = this.selected;
    const discussion = this.attrs.discussion.data;

    await app.request({
      method: 'PATCH',
      url: app.forum.attribute('apiUrl') + '/blomstra/trello/discussions',
      body: {
        selected,
        discussion: discussion.id,
      },
    });

    if (app.current instanceof DiscussionPage) {
      app.current.stream.update();
    }

    m.redraw();

    return this.hide();
  }

  className() {
    return 'Modal--small blomstra-trello-modal';
  }
}
