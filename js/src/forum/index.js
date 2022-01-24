import app from 'flarum/forum/app';
import addModels from './addModels';
import extendDiscussions from './extend/extendDiscussions';

app.initializers.add('blomstra/trello', () => {
  extendDiscussions();
  addModels();
});

export { default as MultiDropdown } from '../common/MultiDropdown';
