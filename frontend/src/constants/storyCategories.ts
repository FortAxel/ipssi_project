/** Values must match `App\Enum\StoryCategory` on the API. */
export const STORY_CATEGORIES = [
  'ADVENTURE',
  'ANIMALS',
  'FAMILY',
  'FANTASY',
  'OTHER',
] as const;

export type StoryCategoryValue = (typeof STORY_CATEGORIES)[number];
