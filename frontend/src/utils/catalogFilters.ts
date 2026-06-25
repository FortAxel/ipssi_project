import { STORY_CATEGORIES, type StoryCategoryValue } from '../constants/storyCategories';
import type { StorySummary } from '../types/api';

export function categoriesPresentIn(stories: StorySummary[]): StoryCategoryValue[] {
  const present = new Set(stories.map((s) => s.category));
  return STORY_CATEGORIES.filter((c) => present.has(c));
}

export function filterStoriesByCategories(
  stories: StorySummary[],
  selected: ReadonlySet<string>,
): StorySummary[] {
  if (selected.size === 0) {
    return stories;
  }
  return stories.filter((s) => selected.has(s.category));
}

export function pruneCategorySelection(
  selected: ReadonlySet<string>,
  available: readonly string[],
): Set<string> {
  const availableSet = new Set(available);
  return new Set([...selected].filter((c) => availableSet.has(c)));
}
