import Model from 'flarum/common/Model';

export default class DatabaseBoard extends Model {
  name = Model.attribute('name');
  shortLink = Model.attribute('shortLink');
}
