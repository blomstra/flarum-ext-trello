import app from 'flarum/admin/app';
import TrelloSettingsPage from './components/ExtensionSettingsPage';

app.initializers.add('blomstra/trello', () => {
  app.extensionData
    .for('blomstra-trello')
    .registerPage(TrelloSettingsPage)
    .registerPermission(
      {
        icon: 'fab fa-trello',
        label: app.translator.trans('blomstra-trello.admin.permissions.add_to_trello'),
        permission: 'discussion.addToTrello',
      },
      'start'
    );
});
