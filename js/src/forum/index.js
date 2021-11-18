import Model from 'flarum/common/Model';
import Discussion from 'flarum/common/models/Discussion';
import app from 'flarum/forum/app';
import addModels from './addModels';
import extendDiscussions from './extend/extendDiscussions';

app.initializers.add('blomstra/trello', () => {
  Discussion.prototype.trelloCardId = Model.attribute('trelloCardId');

  extendDiscussions();
  addModels();
});
