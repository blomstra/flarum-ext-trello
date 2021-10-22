import app from 'flarum/forum/app';
import Model from 'flarum/common/Model';
import Discussion from 'flarum/common/models/Discussion';

app.initializers.add('blomstra/trello', () => {
  Discussion.prototype.trelloCardId = Model.attribute('trelloCardId');
});
