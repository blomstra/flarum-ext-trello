import app from 'flarum/forum/app';
import TrelloBoard from './models/TrelloBoard';
import DatabaseBoard from './models/DatabaseBoard';

export default function () {
  app.store.models['trello-board'] = TrelloBoard;
  app.store.models['database-board'] = DatabaseBoard;
}
