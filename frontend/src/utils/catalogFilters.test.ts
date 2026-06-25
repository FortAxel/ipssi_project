import { describe, expect, it } from 'vitest';
import { STORY_CATEGORIES } from '../constants/storyCategories';
import type { StorySummary } from '../types/api';
import {
  categoriesPresentIn,
  filterStoriesByCategories,
  pruneCategorySelection,
} from './catalogFilters';

const story = (id: number, category: string): StorySummary => ({
  id,
  title: `Story ${id}`,
  description: '',
  coverImage: '',
  category,
  ageRange: '3-6',
  pageCount: 1,
});

describe('catalogFilters', () => {
  it('lists categories present in stories, in enum order', () => {
    const stories = [story(1, 'FANTASY'), story(2, 'ANIMALS')];
    expect(categoriesPresentIn(stories)).toEqual(['ANIMALS', 'FANTASY']);
    expect(categoriesPresentIn(stories)).not.toContain('ADVENTURE');
  });

  it('returns all stories when no category is selected', () => {
    const stories = [story(1, 'FANTASY'), story(2, 'ANIMALS')];
    expect(filterStoriesByCategories(stories, new Set())).toHaveLength(2);
  });

  it('filters by multiple selected categories (OR)', () => {
    const stories = [
      story(1, 'FANTASY'),
      story(2, 'ANIMALS'),
      story(3, 'ADVENTURE'),
    ];
    const filtered = filterStoriesByCategories(stories, new Set(['FANTASY', 'ANIMALS']));
    expect(filtered.map((s) => s.id)).toEqual([1, 2]);
  });

  it('prunes selections that are no longer available', () => {
    const pruned = pruneCategorySelection(new Set(['FANTASY', 'ANIMALS']), ['ANIMALS']);
    expect([...pruned]).toEqual(['ANIMALS']);
  });

  it('keeps enum order for available categories', () => {
    const stories = STORY_CATEGORIES.map((c, i) => story(i, c));
    expect(categoriesPresentIn(stories)).toEqual([...STORY_CATEGORIES]);
  });
});
