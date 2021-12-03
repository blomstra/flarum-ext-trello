import app from 'flarum/forum/app';
import icon from 'flarum/common/helpers/icon';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export interface DatabaseBoard {
  name: string;
  short_link: string;
}
export interface BoardLane {
  name: string;
  id: string;
}

interface IState {
  loading: boolean;
  boards: DatabaseBoard[] | null;
  lanes: BoardLane[] | null;
  mappings: {},
}

export default class SendToTrelloModal extends Modal {
  states: IState = {
    loading: false,
    boards: null,
    lanes: null,
    mappings: {},
  };

  title() {
    return app.translator.trans('blomstra-trello.forum.modals.title');
  }

  content() {
    return (
      <div class="Modal-body">
        <div class="Form">
          <div class="Form-group">
            <label>{app.translator.trans('blomstra-trello.forum.modals.fields.board')}</label>
            {this.states.boards ? (
              <>
              <span class="Select">
                <select
                  class="Select-input FormControl"
                  onchange={(e: InputEvent) => {
                    const target = e.currentTarget as HTMLSelectElement;
                    this.loadTrelloLanes({
                      short_link: target.value,
                      text: target.selectedOptions[0].textContent,
                    });
                  }}
                >
                  {this.states.boards.map((item) => {
                    return this.defaultBoardId == item.short_link ? (
                      <option selected value={item.short_link}>
                        {item.name}
                      </option>
                    ) : (
                      <option value={item.short_link}>{item.name}</option>
                    );
                  })}
                </select>
                {icon('fas fa-sort Select-caret')}
              </span>
              {
                this.defaultBoardMapping.length == 0 ? (
                  <div class="warningText">{app.translator.trans('blomstra-trello.forum.modals.no_mappings_for_board')}</div>
                ) : ('')
              }

              </>
            ) : (
              <p>{app.translator.trans('blomstra-trello.forum.modals.no_available_boards_label')}</p>
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
                    return this.selected.lane == item.attributes.id ? (
                      <option selected value={item.attributes.id}>
                        {item.attributes.name}
                      </option>
                    ) : (
                      <option value={item.attributes.id}>{item.attributes.name}</option>
                    );
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
        </div>
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

    this.defaultBoardId = app.forum.attribute('trelloDefaultBoardId');
    this.defaultLaneId = app.forum.attribute('trelloLastUsedLaneId');
    this.defaultBoardMapping = [];

    this.loadData();
  }

  loadData() {
    this.loading = true;

    this.states.boards = app.forum.attribute('trelloBoards');

    this.loadMappings();

    if (this.states.boards.length) {
      const item = {
        short_link: this.defaultBoardId ? this.defaultBoardId : this.states.boards[0].short_link,
        text: this.states.boards[0].name,
      };

      this.loadTrelloLanes(item);
    }

    m.redraw();

    this.loading = false;
  }

  setCurrentSelectedBoard(item) {
    this.selected.board = {
      short_link: item.short_link,
      text: item.text,
    };

    this.defaultBoardId = item.short_link;

    this.defaultBoardMapping = this.states.mappings[item.short_link];
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
      url: app.forum.attribute('apiUrl') + `/blomstra/trello/api-boards/${this.selected.board.short_link}/lanes`,
    });

    const data = response.data;
    this.states.lanes = data;
    if (this.states.lanes.length) {

      const laneExists = this.states.lanes?.some?.((item) => item.id === this.defaultLaneId);

      if (laneExists) {
        this.setCurrentSelectedLane({ id: this.defaultLaneId });
      } else {
        this.setCurrentSelectedLane({ id: this.states.lanes[0].attributes.id})
      }
    }
    this.disabled = false;

    m.redraw();
  }

  onsubmit(e) {
    e.preventDefault();

    const selected = this.selected;
    const discussion = this.attrs.discussion;

    this.loading = true;

    discussion
      .save({
        trello: selected,
      })
      .then(() => {
        if (app.current instanceof DiscussionPage) {
          app.current.stream.update();
        }

        app.forum.data.attributes.trelloDefaultBoardId = selected.board.short_link;
        app.forum.data.attributes.trelloLastUsedLaneId = selected.lane;
        m.redraw();

        this.loading = false;

        this.hide();
      });
  }

  className() {
    return 'Modal--small blomstra-trello-modal';
  }

  private loadMappings() {
    try {
      this.states.mappings = JSON.parse(app.forum.attribute("labelTagMappings"));
      this.defaultBoardMapping = this.states.mappings[this.defaultBoardId];

    } catch {
      this.states.mappings = {};
      this.defaultBoardMapping = [];
    }
  }
}
