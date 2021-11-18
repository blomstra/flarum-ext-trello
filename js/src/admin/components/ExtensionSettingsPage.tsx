import app from 'flarum/admin/app';
import icon from 'flarum/common/helpers/icon';
import Button from 'flarum/common/components/Button';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';

export interface Board {
  organization(): string;
  boards(): Array;
}

export interface DatabaseBoard {
  name(): string;
  shortLink(): string;
}

interface IState {
  loading: boolean;
  databaseBoards: DatabaseBoard[] | null;
  allBoards: Board[] | null;
}

export default class TrelloSettingsPage extends ExtensionPage {
  states: IState = {
    loading: false,
    databaseBoards: null,
    allBoards: null,
  };

  oninit(vnode) {
    super.oninit(vnode);

    this.loading = false;
    this.currentSelected = false;

    this.loadDatabaseData();
    this.loadTrelloData();
  }

  async loadDatabaseData() {
    this.states.databaseBoards = (
      await app.request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/blomstra/trello/boards',
      })
    ).data;
    m.redraw();
  }

  async loadTrelloData() {
    this.states.allBoards = (
      await app.request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/blomstra/trello/api-boards',
      })
    ).data.attributes;
    m.redraw();

    if (this.states.allBoards) {
      const firstItem = this.states.allBoards[0];
      this.currentSelected = {
        shortLink: firstItem.boards[0].short_link,
        text: firstItem.boards[0].name,
      };
    }
  }

  addBoardToDefault() {
    const selected = this.currentSelected;

    if (selected) {
      this.loading = true;

      app
        .request({
          method: 'POST',
          url: app.forum.attribute('apiUrl') + '/blomstra/trello/boards',
          body: {
            selected,
          },
        })
        .then(
          function (response) {
            const data = response.data;

            if (data.attributes.name) {
              this.states.databaseBoards.push(data);
            }

            m.redraw();

            this.loading = false;
          }.bind(this)
        );
    }
  }

  async removeBoardFromDefault(e: MouseEvent) {
    const shortLink = e.currentTarget.getAttribute('data-id');
    const response = await app.request({
      method: 'DELETE',
      url: app.forum.attribute('apiUrl') + '/blomstra/trello/boards/' + shortLink,
    });

    this.states.databaseBoards = this.states.databaseBoards.filter((value) => value.attributes.shortLink != shortLink);

    this.loading = false;
    m.redraw();
  }

  content() {
    return [
      <div class="container">
        <div class="Form">
          <div class="Form-group">
            <h3>{app.translator.trans('blomstra-trello.admin.settings.label.general')}</h3>
            {this.buildSettingComponent({
              type: 'string',
              setting: 'blomstra-trello.api_key',
              label: app.translator.trans('blomstra-trello.admin.settings.api_key_label'),
              help: app.translator.trans('blomstra-trello.admin.settings.api_key_help'),
            })}
            {this.buildSettingComponent({
              type: 'string',
              setting: 'blomstra-trello.api_token',
              label: app.translator.trans('blomstra-trello.admin.settings.api_token_label'),
              help: app.translator.trans('blomstra-trello.admin.settings.api_token_help'),
            })}
            {this.buildSettingComponent({
              type: 'string',
              setting: 'blomstra-trello.member_id',
              label: app.translator.trans('blomstra-trello.admin.settings.member_id_label'),
              help: app.translator.trans('blomstra-trello.admin.settings.member_id_help'),
            })}
          </div>
          <hr />
          <div class="Form-group">
            <label>{app.translator.trans('blomstra-trello.admin.settings.available_boards_label')}</label>
            {this.states.allBoards ? (
              <div class="TagSettings-rangeInput">
                <span class="Select">
                  <select
                    class="Select-input FormControl"
                    onchange={(e) => {
                      this.currentSelected = {
                        shortLink: e.currentTarget.value,
                        text: e.currentTarget.selectedOptions[0].textContent,
                      };
                    }}
                  >
                    {this.states.allBoards.map((orgData) => {
                      return (
                        <optgroup label={`${orgData.organization}`}>
                          {orgData.boards.map((board) => (
                            <option value={`${board.short_link}`}>{board.name}</option>
                          ))}
                        </optgroup>
                      );
                    })}
                  </select>
                  {icon('fas fa-sort Select-caret')}
                </span>
                <Button className="Button Button--icon" onclick={this.addBoardToDefault.bind(this)} loading={this.loading} icon="fas fa-plus">
                  {app.translator.trans('blomstra-trello.admin.settings.button.add')}
                </Button>
              </div>
            ) : (
              <p>{app.translator.trans('blomstra-trello.admin.settings.no_available_boards_label')}</p>
            )}
          </div>
          <div class="Form-group">
            <label>{app.translator.trans('blomstra-trello.admin.settings.selected_boards_label')}</label>
            {this.states.databaseBoards ? (
              <div class="TagSettings-rangeInput">
                <ul>
                  {this.states.databaseBoards.map((databaseBoard) => {
                    return [
                      <li>
                        <Button
                          className="Button Button--icon"
                          data-id={databaseBoard.attributes.shortLink}
                          onclick={this.removeBoardFromDefault.bind(this)}
                          icon="fas fa-minus"
                        ></Button>{' '}
                        {databaseBoard.attributes.name}
                      </li>,
                    ];
                  })}
                </ul>
              </div>
            ) : (
              <p>{app.translator.trans('blomstra-trello.admin.settings.no_selected_boards_label')}</p>
            )}
          </div>
        </div>
        <div class="Form-group">{this.submitButton()}</div>
      </div>,
    ];
  }
}
