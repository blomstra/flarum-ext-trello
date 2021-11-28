import app from 'flarum/admin/app';

import Button from 'flarum/common/components/Button';
import Modal from 'flarum/common/components/Modal';
import Select from 'flarum/common/components/Select';

import type { Tag } from './ProjectsExtensionPage';

interface IModalAttrs {
  standardTags: Tag[];
  projectTags: Tag[];
  onClose(projectTagId: string, standardTagId: string): void;
}

interface IState {
  projectTag: string | null;
  tag: string | null;
}

export default class NewTagMappingModal extends Modal {
  attrs!: IModalAttrs;

  state: IState = {
    projectTag: null,
    tag: null,
  };

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
          <label>{app.translator.trans('blomstra-list-your-project.admin.settings.mapping.project_tag')}</label>
          <Select
            onchange={(val: string) => (this.state.projectTag = val)}
            options={this.attrs.projectTags.reduce((acc, tag: Tag) => {
              acc[tag.id()] = this.getTagDisplayName(tag);
              return acc;
            }, {} as Record<string, string>)}
          />
        </div>

        <div>
          <label>{app.translator.trans('blomstra-list-your-project.admin.settings.mapping.tag')}</label>
          <Select
            onchange={(val: string) => (this.state.tag = val)}
            options={this.attrs.standardTags.reduce((acc, tag: Tag) => {
              acc[tag.id()] = this.getTagDisplayName(tag);
              return acc;
            }, {} as Record<string, string>)}
          />
        </div>

        <Button
          class="Button Button--primary"
          disabled={this.state.projectTag === null || this.state.tag === null}
          onclick={() => {
            this.attrs.onClose(this.state.projectTag, this.state.tag);
            this.hide();
          }}
        >
          {app.translator.trans('blomstra-list-your-project.admin.settings.mapping.add_new')}
        </Button>
      </div>
    );
  }
}
