import app from 'flarum/admin/app';

import Button from 'flarum/common/components/Button';
import Modal from 'flarum/common/components/Modal';
import Select from 'flarum/common/components/Select';

import {Board, DatabaseBoard, Tag} from "./ExtensionSettingsPage";
import icon from "flarum/common/helpers/icon";
import Separator from "flarum/common/components/Separator";

export interface Label {
  name(): string;
  shortLink(): string;
}

interface IModalAttrs {
  standardTags: Tag[];
  projectTags: Tag[];
  states: IState;
  onClose(boardShortLink: string, label: {}, tagId: string): void;
}

interface IState {
  loading: boolean;
  databaseBoards: DatabaseBoard[] | null;
  allBoards: Board[] | null;
  labels: Label[] | null;
  tags: Tag[] | null;
}

export default class NewTagMappingModal extends Modal {
  attrs!: IModalAttrs;

  oninit(vnode) {
    super.oninit(vnode);

    this.states = this.attrs.states;

    this.tags = this.states.tags?.map(tag => {
      return {
        id: tag.data.id,
        name: tag.data.attributes.name,
        color: tag.data.attributes.color,
        slug: tag.data.attributes.slug,
      };
    })

    this.selected = {
      board: null,
      label: null,
      tag: null
    }
  }

  className() {
    return 'BlomstraListProject-NewTagMappingModal Modal--small';
  }

  title() {

    return app.translator.trans('blomstra-list-your-project.admin.settings.mapping.modal_title');
  }

  getTagDisplayName(tag: Tag) {
    if (!tag.isChild()) return tag.name();

    return tag.parent().name() + ' / ' + tag.name();
  }

  content() {
    return (
      <div class="Modal-body">
        <div>
          <label>{app.translator.trans('blomstra-trello.admin.modals.fields.board')}</label>
          <Select
            onchange={(e: InputEvent) => {
              this.loadTrelloLabels({
                short_link: e,
              });
            }}
            options={this.states.databaseBoards.reduce((acc, curr) => {
              acc[curr.short_link] = curr.name;
              return acc;
            }, {})}
          />
        </div>

        <div>
          <label>{app.translator.trans('blomstra-trello.admin.modals.fields.trello_label')}</label>
          {this.states.labels ? (
              <span class="Select">
                <select
                  class="Select-input FormControl"
                  onchange={(e: InputEvent) => {
                    const target = e.currentTarget as HTMLSelectElement;
                    const colorWithNameArr = target.selectedOptions[0].textContent.split(' ');
                    let color = colorWithNameArr[0].replace(/[{()}]/g, '');

                    this.selected.label = {
                      id: target.value,
                      name: target.selectedOptions[0].textContent.split(' ')[1] || app.translator.trans('blomstra-trello.admin.settings.no_label_name'),
                      color,
                    }

                    console.log(this.selected.label)
                  }}
                >
                  {this.states.labels.map((item) => {

                    return (
                      <option value={item.attributes.id}>{'(' + item.attributes.color + ') ' + item.attributes.name }</option>
                    );
                  })}
                </select>
                {icon('fas fa-sort Select-caret')}
              </span>
          ) : (
            <p>{app.translator.trans('blomstra-trello.admin.modals.no_available_labels_label')}</p>
          )}

        </div>

        <div>
          <label>{app.translator.trans('blomstra-trello.admin.modals.fields.forum_tags')}</label>
          {this.tags ? (
            <Select
              onchange={(e: InputEvent) => {
                this.selected.tag = e;
              }}
              options={this.tags?.reduce((acc, curr) => {
                acc[curr.id] = curr.name;
                return acc;
              }, {})}
            />
          ) : (
            <p>{app.translator.trans('blomstra-trello.admin.modals.no_available_tags_label')}</p>
          )}

        </div>

        <Button
          class="Button Button--primary"
          disabled={this.selected.board === null || this.selected.label === null || this.selected.tag === null}
          onclick={() => {
            this.attrs.onClose(this.selected.board, this.selected.label, this.selected.tag);
            this.hide();
          }}
        >
          {app.translator.trans('blomstra-list-your-project.admin.settings.mapping.add_new')}
        </Button>
      </div>
    );
  }

  private async loadTrelloLabels(param: { short_link: string }) {

    // load labels based on the currently selected board
    const response = await app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + `/blomstra/trello/api-boards/${param.short_link}/labels`,
    });

    this.states.labels = response.data;

    let label = this.states.labels[0]

    this.selected.board = param.short_link;
    this.selected.label = {
      id: label.id,
      name: label.attributes.name,
      color: label.attributes.color
    }

    m.redraw();
  }
}
