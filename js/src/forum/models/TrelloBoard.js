import Model from 'flarum/common/Model';

export default class TrelloBoard extends Model {
  organization = Model.attribute('organization');
  boards = Model.attribute('boards');
}
