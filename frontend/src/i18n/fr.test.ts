import { describe, expect, it } from 'vitest';
import { labels } from './fr';

describe('labels (fr)', () => {
  it('expose les libellés du catalogue et du profil', () => {
    expect(labels.catalog).toBe('Catalogue');
    expect(labels.profileHistory).toBe('Historique');
    expect(labels.favorites).toBe('Favoris');
  });

  it('formate la pagination de lecture', () => {
    expect(labels.pageOf(2, 10)).toBe('Page 2 sur 10');
  });
});
