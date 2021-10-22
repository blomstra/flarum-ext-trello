import app from 'flarum/admin/app';

app.initializers.add('blomstra/trello', () => {
  app.extensionData
    .for('blomstra-trello')
    .registerSetting({
      setting: `blomstra-trello.api_key`,
      label: app.translator.trans('blomstra-trello.admin.settings.api_key_label'),
      help: app.translator.trans('blomstra-trello.admin.settings.api_key_help'),
      type: 'string',
    })
    .registerPermission(
      {
        icon: 'fab fa-trello',
        label: app.translator.trans('blomstra-trello.admin.permissions.add_to_trello'),
        permission: 'discussion.addToTrello',
      },
      'start'
    );
});
